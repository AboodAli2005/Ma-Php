<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        // Check if item exists in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_quantity, $row['id']);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
        }
        header("Location: cart.php");
        exit();
    } elseif ($action === 'update') {
        $cart_id = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
        }
        header("Location: cart.php");
        exit();
    } elseif ($action === 'remove') {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        header("Location: cart.php");
        exit();
    } elseif ($action === 'checkout') {
        $conn->begin_transaction();
        try {
            // Get all cart items
            $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.price FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $items = $stmt->get_result();

            if ($items->num_rows > 0) {
                $total_amount = 0;
                $order_items = [];
                while ($row = $items->fetch_assoc()) {
                    $total_amount += $row['price'] * $row['quantity'];
                    $order_items[] = $row;
                }
                
                // Add tax
                $tax = $total_amount * 0.08;
                $final_amount = $total_amount + $tax;

                // Create Order
                $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
                $stmt->bind_param("id", $user_id, $final_amount);
                $stmt->execute();
                $order_id = $conn->insert_id;

                // Insert order items
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                foreach ($order_items as $item) {
                    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                    $stmt->execute();
                }

                // Clear Cart
                $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                $conn->commit();
                $success_message = "Order placed successfully! Order ID: #" . $order_id;
            } else {
                $error_message = "Your cart is empty.";
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error placing order: " . $e->getMessage();
        }
    }
}

// Fetch Cart
$stmt = $conn->prepare("SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image_url FROM cart_items c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$subtotal = 0;
$item_count = 0;
$cart_items = [];

while ($row = $cart_result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
    $item_count += $row['quantity'];
}

$tax = $subtotal * 0.08;
$total = $subtotal + $tax;

require_once 'includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-lg">
<div class="flex flex-col lg:flex-row gap-gutter">
<!-- Cart Items List -->
<div class="flex-grow flex flex-col gap-sm">
<h1 class="font-headline-lg text-headline-lg mb-md">Your Cart</h1>

<?php if ($success_message): ?>
    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>
<?php if ($error_message): ?>
    <div class="bg-error-container text-on-error-container p-4 rounded mb-4">
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php endif; ?>

<?php if (empty($cart_items)): ?>
    <div class="bg-surface-container-lowest rounded-lg p-md text-center border border-surface-container-low">
        <p class="text-secondary mb-4">Your cart is currently empty.</p>
        <a href="products.php" class="bg-primary text-on-primary py-2 px-6 rounded hover:opacity-90 transition-opacity">Browse Products</a>
    </div>
<?php else: ?>
    <?php foreach ($cart_items as $item): ?>
    <div class="bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] p-md flex flex-col sm:flex-row items-center gap-md border border-surface-container-low">
    <img alt="<?= htmlspecialchars($item['name']) ?>" class="w-32 h-32 object-cover rounded-md" src="<?= htmlspecialchars($item['image_url']) ?>"/>
    <div class="flex-grow flex flex-col gap-base">
    <h2 class="font-headline-sm text-headline-sm text-on-surface"><a href="product.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['name']) ?></a></h2>
    <p class="font-label-md text-label-md text-primary mt-2">$<?= number_format($item['price'], 2) ?></p>
    </div>
    <div class="flex flex-col items-end gap-sm w-full sm:w-auto">
    <form action="cart.php" method="POST" class="flex items-center border border-outline-variant rounded">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
        <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>" aria-label="Decrease quantity" class="px-3 py-1 text-on-surface-variant font-label-md transition-colors hover:bg-surface-container-low">-</button>
        <span class="px-4 font-body-md text-on-surface"><?= $item['quantity'] ?></span>
        <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" aria-label="Increase quantity" class="px-3 py-1 text-on-surface-variant font-label-md transition-colors hover:bg-surface-container-low">+</button>
    </form>
    <form action="cart.php" method="POST">
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
        <button type="submit" class="font-label-sm text-label-sm text-error hover:text-on-error-container transition-colors flex items-center gap-1 mt-2 bg-transparent border-none cursor-pointer">
        <span class="material-symbols-outlined text-[16px]" data-icon="delete">delete</span> Remove
        </button>
    </form>
    </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<!-- Summary Card -->
<?php if (!empty($cart_items)): ?>
<div class="w-full lg:w-96 flex-shrink-0">
<div class="bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] p-md border border-surface-container-low sticky top-24">
<h2 class="font-headline-sm text-headline-sm mb-md border-b border-outline-variant pb-xs">Order Summary</h2>
<div class="flex flex-col gap-sm mb-md font-body-sm text-body-sm text-on-surface-variant">
<div class="flex justify-between">
<span>Subtotal (<?= $item_count ?> items)</span>
<span class="font-medium text-on-surface">$<?= number_format($subtotal, 2) ?></span>
</div>
<div class="flex justify-between">
<span>Shipping</span>
<span class="font-medium text-on-surface">Free</span>
</div>
<div class="flex justify-between">
<span>Estimated Tax (8%)</span>
<span class="font-medium text-on-surface">$<?= number_format($tax, 2) ?></span>
</div>
</div>
<div class="flex justify-between items-center border-t border-outline-variant pt-sm mb-md">
<span class="font-headline-sm text-headline-sm text-on-surface">Total</span>
<span class="font-headline-sm text-headline-sm text-primary">$<?= number_format($total, 2) ?></span>
</div>
<form action="cart.php" method="POST">
    <input type="hidden" name="action" value="checkout">
    <button type="submit" class="w-full bg-primary text-on-primary font-label-md text-label-md py-3 rounded hover:bg-surface-tint transition-colors flex justify-center items-center gap-2 cursor-pointer border-none">
        Checkout
        <span class="material-symbols-outlined text-[18px]" data-icon="arrow_forward">arrow_forward</span>
    </button>
</form>
<div class="mt-sm text-center mt-4">
<a class="font-body-sm text-body-sm text-secondary hover:text-primary underline transition-colors" href="products.php">Continue Shopping</a>
</div>
</div>
</div>
<?php endif; ?>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>

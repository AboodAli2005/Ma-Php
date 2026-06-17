<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='text-center py-20 text-xl'>Product not found. <a href='products.php' class='text-primary underline'>Go back to products.</a></div>";
    require_once 'includes/footer.php';
    exit();
}

$product = $result->fetch_assoc();
?>

<!-- Main Content -->
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-desktop py-xl">
<!-- Breadcrumb / Back Link -->
<div class="mb-lg">
<a class="inline-flex items-center gap-xs font-label-md text-label-md text-secondary hover:text-primary transition-colors cursor-pointer" href="products.php">
<span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back to Products
            </a>
</div>
<!-- Product Details Bento Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Product Image Section (Left Column) -->
<div class="lg:col-span-7 flex flex-col gap-sm">
<div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant h-[600px] relative flex items-center justify-center">
<img alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars($product['image_url']) ?>"/>
</div>
</div>
<!-- Product Info Section (Right Column) -->
<div class="lg:col-span-5 flex flex-col gap-lg px-md lg:px-0">
<!-- Header Info -->
<div class="flex flex-col gap-sm">
<div class="flex items-center gap-sm">
<span class="inline-flex items-center px-2 py-1 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider"><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></span>
</div>
<h1 class="font-headline-xl text-headline-xl text-primary mt-2"><?= htmlspecialchars($product['name']) ?></h1>
<div class="flex items-baseline gap-md mt-1">
<span class="font-headline-lg text-headline-lg text-primary">$<?= number_format($product['price'], 2) ?></span>
</div>
</div>
<!-- Description -->
<div class="prose prose-sm max-w-none font-body-md text-body-md text-on-surface-variant">
<p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
</div>
<!-- Divider -->
<hr class="border-t border-outline-variant w-full"/>
<!-- Selection & Actions -->
<form action="cart.php" method="POST" class="flex flex-col gap-md">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
    
<!-- Quantity -->
<div class="flex flex-col gap-xs">
<label class="font-label-sm text-label-sm text-primary uppercase tracking-wider" for="quantity">Quantity</label>
<div class="flex items-center border border-outline-variant rounded-lg w-max bg-surface-container-lowest h-12">
<button type="button" onclick="document.getElementById('quantity').stepDown()" aria-label="Decrease quantity" class="px-4 text-secondary hover:text-primary transition-colors h-full flex items-center justify-center cursor-pointer active:opacity-70">
<span class="material-symbols-outlined">remove</span>
</button>
<input name="quantity" class="w-16 text-center font-body-md text-body-md border-none focus:ring-0 bg-transparent p-0 m-0 text-primary h-full" id="quantity" max="100" min="1" type="number" value="1"/>
<button type="button" onclick="document.getElementById('quantity').stepUp()" aria-label="Increase quantity" class="px-4 text-secondary hover:text-primary transition-colors h-full flex items-center justify-center cursor-pointer active:opacity-70">
<span class="material-symbols-outlined">add</span>
</button>
</div>
</div>
<!-- Add to Cart Button -->
<button type="submit" class="w-full bg-[#0f172a] hover:bg-[#1e293b] text-white font-label-md text-label-md py-4 rounded-lg flex justify-center items-center gap-sm transition-all duration-200 cursor-pointer shadow-sm active:scale-[0.99] mt-2">
<span class="material-symbols-outlined">shopping_bag</span>
                        Add to Cart
                    </button>
</form>
<!-- Assurance / Info Blocks -->
<div class="grid grid-cols-2 gap-sm mt-md">
<div class="bg-surface-container-low p-4 rounded-lg flex flex-col gap-xs items-center text-center">
<span class="material-symbols-outlined text-secondary text-[24px]">local_shipping</span>
<span class="font-label-sm text-label-sm text-primary">Free Global Shipping</span>
<span class="font-body-sm text-body-sm text-secondary">On orders over $200</span>
</div>
<div class="bg-surface-container-low p-4 rounded-lg flex flex-col gap-xs items-center text-center">
<span class="material-symbols-outlined text-secondary text-[24px]">verified</span>
<span class="font-label-sm text-label-sm text-primary">2 Year Warranty</span>
<span class="font-body-sm text-body-sm text-secondary">Full coverage included</span>
</div>
</div>
</div>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>

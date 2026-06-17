<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "Product deleted successfully.";
        } else {
            $error_message = "Error deleting product.";
        }
    } elseif ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name']);
        $category_id = (int)$_POST['category_id'];
        $price = (float)$_POST['price'];
        $description = trim($_POST['description']);
        $image_url = trim($_POST['image_url']);

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $category_id, $name, $description, $price, $image_url);
            if ($stmt->execute()) {
                $success_message = "Product added successfully.";
            } else {
                $error_message = "Error adding product.";
            }
        } else {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, image_url=? WHERE id=?");
            $stmt->bind_param("issdsi", $category_id, $name, $description, $price, $image_url, $id);
            if ($stmt->execute()) {
                $success_message = "Product updated successfully.";
            } else {
                $error_message = "Error updating product.";
            }
        }
    }
}

// Fetch Categories
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

// Fetch Products
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$products_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Admin Panel - Products</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
        "colors": {
                "on-tertiary-container": "#0c9488",
                "surface-container-highest": "#e0e3e5",
                "surface-container-lowest": "#ffffff",
                "tertiary-container": "#00201d",
                "tertiary-fixed-dim": "#6bd8cb",
                "on-secondary-fixed-variant": "#38485d",
                "inverse-primary": "#bec6e0",
                "on-primary": "#ffffff",
                "surface-tint": "#565e74",
                "inverse-surface": "#2d3133",
                "on-tertiary-fixed": "#00201d",
                "surface-bright": "#f7f9fb",
                "primary-fixed-dim": "#bec6e0",
                "on-secondary": "#ffffff",
                "on-error-container": "#93000a",
                "on-surface-variant": "#45464d",
                "secondary-fixed": "#d3e4fe",
                "on-error": "#ffffff",
                "inverse-on-surface": "#eff1f3",
                "on-secondary-fixed": "#0b1c30",
                "on-primary-fixed-variant": "#3f465c",
                "secondary-container": "#d0e1fb",
                "surface-variant": "#e0e3e5",
                "error": "#ba1a1a",
                "tertiary": "#000000",
                "primary-container": "#131b2e",
                "surface-container": "#eceef0",
                "on-primary-container": "#7c839b",
                "tertiary-fixed": "#89f5e7",
                "error-container": "#ffdad6",
                "surface-container-low": "#f2f4f6",
                "on-surface": "#191c1e",
                "primary-fixed": "#dae2fd",
                "on-tertiary-fixed-variant": "#005049",
                "outline-variant": "#c6c6cd",
                "secondary": "#505f76",
                "surface-dim": "#d8dadc",
                "on-tertiary": "#ffffff",
                "on-background": "#191c1e",
                "secondary-fixed-dim": "#b7c8e1",
                "primary": "#000000",
                "surface-container-high": "#e6e8ea",
                "background": "#f7f9fb"
        }
        }
    }
    }
</script>
<style>
    .ambient-shadow { box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.05); }
    .modal { display: none; }
    .modal.active { display: flex; }
</style>
</head>
<body class="bg-background text-on-background font-body-md h-screen flex overflow-hidden">

<?php require_once 'includes/sidebar.php'; ?>

<main class="flex-1 md:ml-64 h-full overflow-y-auto bg-background p-4 md:p-8">
<div class="max-w-container-max mx-auto space-y-8">

<?php if ($success_message): ?>
    <div class="bg-green-100 text-green-800 p-4 rounded"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>
<?php if ($error_message): ?>
    <div class="bg-error-container text-on-error-container p-4 rounded"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
<div>
<h1 class="text-3xl font-bold text-primary">Products</h1>
<p class="text-secondary mt-1">Manage your store's inventory and catalog.</p>
</div>
<div class="flex items-center gap-3 w-full md:w-auto">
<button onclick="openModal()" class="bg-primary text-on-primary rounded-lg py-2 px-4 font-medium flex items-center gap-2 hover:bg-primary/90 transition-colors">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
    Add Product
</button>
</div>
</header>

<!-- Table Card -->
<div class="bg-surface-container-lowest rounded-lg ambient-shadow border border-outline-variant overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low border-b border-outline-variant">
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">ID</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Product Info</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Category</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Price</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/50">
<?php if ($products_result && $products_result->num_rows > 0): ?>
    <?php while($row = $products_result->fetch_assoc()): ?>
    <tr class="hover:bg-surface-container-low/50 transition-colors group">
    <td class="py-4 px-4 text-secondary">#PRD-<?= $row['id'] ?></td>
    <td class="py-4 px-4">
    <div class="flex items-center gap-3">
    <div class="w-10 h-10 rounded bg-surface-container-high overflow-hidden shrink-0 border border-outline-variant">
    <img alt="<?= htmlspecialchars($row['name']) ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars($row['image_url']) ?>"/>
    </div>
    <div>
    <p class="font-medium text-primary"><?= htmlspecialchars($row['name']) ?></p>
    </div>
    </div>
    </td>
    <td class="py-4 px-4">
    <span class="inline-flex items-center px-2 py-1 rounded-full bg-surface-container-high text-on-surface-variant text-xs"><?= htmlspecialchars($row['category_name'] ?? 'None') ?></span>
    </td>
    <td class="py-4 px-4 font-medium text-primary">$<?= number_format($row['price'], 2) ?></td>
    <td class="py-4 px-4 text-right">
    <div class="flex items-center justify-end gap-2">
    <button onclick='editProduct(<?= json_encode($row) ?>)' class="p-2 text-secondary hover:text-primary hover:bg-surface-container-high rounded-md transition-colors" title="Edit">
    <span class="material-symbols-outlined text-[20px]">edit</span>
    </button>
    <form action="products.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <button type="submit" class="p-2 text-secondary hover:text-error hover:bg-error-container/50 rounded-md transition-colors" title="Delete">
        <span class="material-symbols-outlined text-[20px]">delete</span>
        </button>
    </form>
    </div>
    </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center py-4 text-secondary">No products found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</main>

<!-- Modal -->
<div id="productModal" class="modal fixed inset-0 bg-black/50 z-[100] items-center justify-center p-4">
<div class="bg-surface-container-lowest rounded-lg p-6 max-w-md w-full shadow-lg relative">
<button onclick="closeModal()" class="absolute top-4 right-4 text-secondary hover:text-primary"><span class="material-symbols-outlined">close</span></button>
<h2 id="modalTitle" class="text-xl font-bold mb-4">Add Product</h2>
<form action="products.php" method="POST" class="flex flex-col gap-4">
    <input type="hidden" name="action" id="formAction" value="add">
    <input type="hidden" name="id" id="productId" value="">
    
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" id="productName" required class="w-full border border-outline-variant rounded p-2 outline-none focus:border-primary">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Category</label>
        <select name="category_id" id="productCategory" required class="w-full border border-outline-variant rounded p-2 outline-none focus:border-primary">
            <option value="">Select Category</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Price</label>
        <input type="number" step="0.01" name="price" id="productPrice" required class="w-full border border-outline-variant rounded p-2 outline-none focus:border-primary">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Image URL</label>
        <input type="text" name="image_url" id="productImage" class="w-full border border-outline-variant rounded p-2 outline-none focus:border-primary">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" id="productDesc" rows="3" class="w-full border border-outline-variant rounded p-2 outline-none focus:border-primary"></textarea>
    </div>
    <div class="flex justify-end gap-2 mt-2">
        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-outline-variant rounded hover:bg-surface-container-low transition-colors">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded hover:bg-primary/90 transition-colors">Save</button>
    </div>
</form>
</div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Add Product';
        document.getElementById('formAction').value = 'add';
        document.getElementById('productId').value = '';
        document.getElementById('productName').value = '';
        document.getElementById('productCategory').value = '';
        document.getElementById('productPrice').value = '';
        document.getElementById('productImage').value = '';
        document.getElementById('productDesc').value = '';
        document.getElementById('productModal').classList.add('active');
    }

    function editProduct(product) {
        document.getElementById('modalTitle').textContent = 'Edit Product';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productCategory').value = product.category_id;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productImage').value = product.image_url;
        document.getElementById('productDesc').value = product.description;
        document.getElementById('productModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('productModal').classList.remove('active');
    }
</script>

</body></html>

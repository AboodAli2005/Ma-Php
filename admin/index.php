<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch Metrics
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_messages = $conn->query("SELECT COUNT(*) FROM contacts")->fetch_row()[0];

// Fetch Recent Orders
$recent_orders = $conn->query("SELECT o.id, u.name, o.total_amount, o.status, o.created_at FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
    .ambient-shadow-1 { box-shadow: 0px 4px 20px rgba(15, 23, 42, 0.05); }
    .ambient-shadow-2 { box-shadow: 0px 8px 30px rgba(15, 23, 42, 0.08); }
</style>
</head>
<body class="bg-background text-on-background font-body-md text-body-md antialiased flex h-screen overflow-hidden">

<?php require_once 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="flex-1 md:ml-64 h-full overflow-y-auto w-full p-4 md:p-8 lg:p-10 max-w-[1600px] mx-auto">
<header class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
<div>
<h1 class="text-3xl font-bold text-primary mb-2">Platform Overview</h1>
<p class="text-on-surface-variant">Real-time metrics and system status.</p>
</div>
</header>
<!-- Summary Cards Grid -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
<!-- Total Products -->
<div class="bg-surface-container-lowest rounded-lg p-6 ambient-shadow-1 border border-outline-variant/50 flex flex-col justify-between">
<div class="flex justify-between items-start mb-6">
<div class="w-12 h-12 bg-surface-container flex items-center justify-center rounded-lg text-secondary">
<span class="material-symbols-outlined">inventory_2</span>
</div>
</div>
<div>
<h3 class="text-sm font-medium text-on-surface-variant mb-1">Total Products</h3>
<div class="text-3xl font-semibold text-primary"><?= number_format($total_products) ?></div>
</div>
</div>
<!-- Total Orders -->
<div class="bg-surface-container-lowest rounded-lg p-6 ambient-shadow-1 border border-outline-variant/50 flex flex-col justify-between">
<div class="flex justify-between items-start mb-6">
<div class="w-12 h-12 bg-surface-container flex items-center justify-center rounded-lg text-secondary">
<span class="material-symbols-outlined">shopping_cart</span>
</div>
</div>
<div>
<h3 class="text-sm font-medium text-on-surface-variant mb-1">Total Orders</h3>
<div class="text-3xl font-semibold text-primary"><?= number_format($total_orders) ?></div>
</div>
</div>
<!-- Total Users -->
<div class="bg-surface-container-lowest rounded-lg p-6 ambient-shadow-1 border border-outline-variant/50 flex flex-col justify-between">
<div class="flex justify-between items-start mb-6">
<div class="w-12 h-12 bg-surface-container flex items-center justify-center rounded-lg text-secondary">
<span class="material-symbols-outlined">group</span>
</div>
</div>
<div>
<h3 class="text-sm font-medium text-on-surface-variant mb-1">Total Users</h3>
<div class="text-3xl font-semibold text-primary"><?= number_format($total_users) ?></div>
</div>
</div>
<!-- Total Messages -->
<div class="bg-surface-container-lowest rounded-lg p-6 ambient-shadow-1 border border-outline-variant/50 flex flex-col justify-between">
<div class="flex justify-between items-start mb-6">
<div class="w-12 h-12 bg-surface-container flex items-center justify-center rounded-lg text-secondary">
<span class="material-symbols-outlined">forum</span>
</div>
</div>
<div>
<h3 class="text-sm font-medium text-on-surface-variant mb-1">Total Messages</h3>
<div class="text-3xl font-semibold text-primary"><?= number_format($total_messages) ?></div>
</div>
</div>
</section>

<!-- Lower Section Layout -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<!-- Recent Activity Table (Takes 3 columns) -->
<div class="lg:col-span-3 bg-surface-container-lowest rounded-lg p-6 ambient-shadow-1 border border-outline-variant/50 flex flex-col">
<div class="flex items-center justify-between mb-6">
<h2 class="text-xl font-semibold text-primary">Recent Orders</h2>
<a href="orders.php" class="text-sm font-medium text-on-tertiary-container hover:underline">View All</a>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="border-b border-outline-variant/50">
<th class="py-3 px-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Order</th>
<th class="py-3 px-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Status</th>
<th class="py-3 px-4 text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Date</th>
</tr>
</thead>
<tbody class="text-sm text-on-surface">
<?php if($recent_orders->num_rows > 0): ?>
    <?php while($order = $recent_orders->fetch_assoc()): ?>
    <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low transition-colors">
    <td class="py-4 px-4 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center">
    <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
    </div>
    <div>
    <div class="font-medium">Order #<?= $order['id'] ?> ($<?= number_format($order['total_amount'], 2) ?>)</div>
    <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($order['name']) ?></div>
    </div>
    </td>
    <td class="py-4 px-4">
        <?php if ($order['status'] === 'completed'): ?>
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
        <?php elseif ($order['status'] === 'cancelled'): ?>
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
        <?php else: ?>
            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
        <?php endif; ?>
    </td>
    <td class="py-4 px-4 text-on-surface-variant text-xs"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="3" class="py-4 text-center text-on-surface-variant">No recent orders found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</section>
<div class="h-16"></div> <!-- Bottom padding -->
</main>
</body></html>

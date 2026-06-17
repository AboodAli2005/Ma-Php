<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        $success_message = "Order status updated successfully.";
    } else {
        $error_message = "Error updating order status.";
    }
}

// Fetch Orders
$query = "SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$orders_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Admin Panel - Orders</title>
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
<h1 class="text-3xl font-bold text-primary">Orders</h1>
<p class="text-secondary mt-1">Manage customer orders.</p>
</div>
</header>

<div class="bg-surface-container-lowest rounded-lg ambient-shadow border border-outline-variant overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low border-b border-outline-variant">
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Order ID</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Customer</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Date</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Total</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider">Status</th>
<th class="py-3 px-4 font-semibold text-xs text-secondary uppercase tracking-wider text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/50">
<?php if ($orders_result && $orders_result->num_rows > 0): ?>
    <?php while($row = $orders_result->fetch_assoc()): ?>
    <tr class="hover:bg-surface-container-low/50 transition-colors group">
    <td class="py-4 px-4 text-secondary font-medium">#ORD-<?= $row['id'] ?></td>
    <td class="py-4 px-4">
        <p class="text-primary font-medium"><?= htmlspecialchars($row['name']) ?></p>
        <p class="text-secondary text-xs"><?= htmlspecialchars($row['email']) ?></p>
    </td>
    <td class="py-4 px-4 text-secondary text-sm"><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></td>
    <td class="py-4 px-4 text-primary font-medium">$<?= number_format($row['total_amount'], 2) ?></td>
    <td class="py-4 px-4">
        <?php if ($row['status'] === 'completed'): ?>
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
        <?php elseif ($row['status'] === 'cancelled'): ?>
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Cancelled</span>
        <?php else: ?>
            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
        <?php endif; ?>
    </td>
    <td class="py-4 px-4 text-right">
        <form action="orders.php" method="POST" class="inline-flex items-center gap-2">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <select name="status" class="border border-outline-variant rounded p-1 text-sm outline-none focus:border-primary">
                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
            <button type="submit" class="bg-surface-container-high hover:bg-surface-variant text-on-surface p-1 rounded transition-colors text-sm font-medium">Update</button>
        </form>
    </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center py-4 text-secondary">No orders found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</main>
</body></html>

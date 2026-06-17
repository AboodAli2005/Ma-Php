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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    if (empty($name) || empty($email)) {
        $error_message = 'Name and email are required.';
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        if ($stmt->execute()) {
            $success_message = 'Profile updated successfully.';
            $_SESSION['user_name'] = $name;
        } else {
            $error_message = 'Failed to update profile.';
        }
    }
}

// Fetch User Info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();

// Fetch Orders
$stmt = $conn->prepare("SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

require_once 'includes/header.php';
?>

<!-- Main Content Area -->
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-lg">
<div class="mb-lg">
<h1 class="font-headline-xl text-headline-xl-mobile md:text-headline-xl text-primary">My Profile</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-2">Manage your account settings and view past orders.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Sidebar / Settings Navigation -->
<aside class="lg:col-span-3">
<nav class="flex flex-col gap-2">
<a class="flex items-center gap-3 px-4 py-3 bg-primary-container text-on-primary-container rounded-lg font-label-md text-label-md transition-all" href="#account-details">
<span class="material-symbols-outlined" data-icon="person">person</span>
                        Account Details
                    </a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg font-label-md text-label-md transition-all" href="#orders">
<span class="material-symbols-outlined" data-icon="receipt_long">receipt_long</span>
                        Order History
                    </a>
<a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high rounded-lg font-label-md text-label-md transition-all mt-4 text-error hover:text-error hover:bg-error-container/20" href="logout.php">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
                        Logout
                    </a>
</nav>
</aside>
<!-- Main Content Panels -->
<div class="lg:col-span-9 flex flex-col gap-xl">

<?php if ($success_message): ?>
    <div class="bg-green-100 text-green-800 p-4 rounded">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>
<?php if ($error_message): ?>
    <div class="bg-error-container text-on-error-container p-4 rounded">
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php endif; ?>

<!-- Profile Information Card -->
<section class="bg-surface-container-lowest rounded-lg p-md shadow-[0_4px_20px_rgba(15,23,42,0.05)] border border-transparent" id="account-details">
<div class="flex justify-between items-center mb-md pb-xs border-b border-outline-variant">
<h2 class="font-headline-sm text-headline-sm text-primary">Personal Information</h2>
<button id="editBtn" class="text-on-tertiary-container hover:text-on-tertiary-fixed-variant font-label-md text-label-md flex items-center gap-1 transition-colors border-none bg-transparent cursor-pointer">
<span class="material-symbols-outlined" data-icon="edit" style="font-size: 18px;">edit</span>
                            Edit Profile
                        </button>
</div>
<form action="profile.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-md">
<input type="hidden" name="action" value="update_profile">
<div class="flex flex-col gap-1">
<label class="font-label-sm text-label-sm text-secondary">Full Name</label>
<input name="name" class="profile-input px-3 py-2 border border-outline-variant rounded font-body-md text-body-md text-primary bg-surface-bright focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all" readonly type="text" value="<?= htmlspecialchars($user_info['name']) ?>"/>
</div>
<div class="flex flex-col gap-1">
<label class="font-label-sm text-label-sm text-secondary">Email Address</label>
<input name="email" class="profile-input px-3 py-2 border border-outline-variant rounded font-body-md text-body-md text-primary bg-surface-bright focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all" readonly type="email" value="<?= htmlspecialchars($user_info['email']) ?>"/>
</div>

<!-- Hidden Save Actions -->
<div id="actionButtons" class="md:col-span-2 flex justify-end gap-3 hidden mt-2">
<button id="cancelBtn" class="px-4 py-2 border border-outline rounded text-primary font-label-md text-label-md hover:bg-surface-container-low transition-colors cursor-pointer" type="button">Cancel</button>
<button class="px-4 py-2 bg-primary text-on-primary rounded font-label-md text-label-md hover:bg-primary/90 transition-colors cursor-pointer border-none" type="submit">Save Changes</button>
</div>
</form>
</section>

<!-- Order History Table -->
<section class="bg-surface-container-lowest rounded-lg p-md shadow-[0_4px_20px_rgba(15,23,42,0.05)] border border-transparent" id="orders">
<div class="mb-md pb-xs border-b border-outline-variant">
<h2 class="font-headline-sm text-headline-sm text-primary">Order History</h2>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="border-b border-outline-variant">
<th class="py-3 px-4 font-label-md text-label-md text-secondary">Order ID</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary">Date</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary text-right">Total</th>
<th class="py-3 px-4 font-label-md text-label-md text-secondary">Status</th>
</tr>
</thead>
<tbody class="font-body-sm text-body-sm text-primary">
<?php if ($orders_result && $orders_result->num_rows > 0): ?>
    <?php while ($order = $orders_result->fetch_assoc()): ?>
    <tr class="hover:bg-surface-container-low transition-colors border-b border-surface-container-high last:border-b-0">
    <td class="py-4 px-4 font-medium">#ORD-<?= $order['id'] ?></td>
    <td class="py-4 px-4 text-on-surface-variant"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
    <td class="py-4 px-4 text-right font-medium">$<?= number_format($order['total_amount'], 2) ?></td>
    <td class="py-4 px-4">
        <?php if ($order['status'] === 'completed'): ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
        <?php elseif ($order['status'] === 'cancelled'): ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
        <?php else: ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
        <?php endif; ?>
    </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="4" class="py-4 px-4 text-center text-on-surface-variant">You have no past orders.</td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</div>
</section>
</div>
</div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editBtn = document.getElementById('editBtn');
        const inputs = document.querySelectorAll('.profile-input');
        const actionButtons = document.getElementById('actionButtons');
        const cancelBtn = document.getElementById('cancelBtn');

        if(editBtn && inputs.length && actionButtons) {
            editBtn.addEventListener('click', (e) => {
                e.preventDefault();
                inputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.classList.remove('bg-surface-bright');
                    input.classList.add('bg-surface-container-lowest');
                });
                actionButtons.classList.remove('hidden');
                inputs[0].focus();
            });

            if(cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    inputs.forEach(input => {
                        input.setAttribute('readonly', true);
                        input.classList.add('bg-surface-bright');
                        input.classList.remove('bg-surface-container-lowest');
                        // Restore original value (optional enhancement)
                        input.value = input.defaultValue;
                    });
                    actionButtons.classList.add('hidden');
                });
            }
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>

<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_message = "Message deleted successfully.";
    } else {
        $error_message = "Error deleting message.";
    }
}

// Fetch Messages
$query = "SELECT * FROM contacts ORDER BY created_at DESC";
$messages_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Admin Panel - Messages</title>
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
<h1 class="text-3xl font-bold text-primary">Messages</h1>
<p class="text-secondary mt-1">Review contact form submissions.</p>
</div>
</header>

<div class="space-y-4">
<?php if ($messages_result && $messages_result->num_rows > 0): ?>
    <?php while($msg = $messages_result->fetch_assoc()): ?>
    <div class="bg-surface-container-lowest rounded-lg p-6 ambient-shadow border border-outline-variant">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="font-bold text-primary text-lg"><?= htmlspecialchars($msg['name']) ?></h3>
                <p class="text-sm text-secondary"><?= htmlspecialchars($msg['email']) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-on-surface-variant mb-2"><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></p>
                <form action="messages.php" method="POST" onsubmit="return confirm('Delete this message?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                    <button type="submit" class="text-error hover:text-red-700 text-sm font-medium flex items-center justify-end gap-1">
                        <span class="material-symbols-outlined text-[16px]">delete</span> Delete
                    </button>
                </form>
            </div>
        </div>
        <div class="bg-surface-bright p-4 rounded-md border border-outline-variant/50">
            <p class="whitespace-pre-wrap text-on-surface"><?= htmlspecialchars($msg['message']) ?></p>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="bg-surface-container-lowest rounded-lg p-8 text-center ambient-shadow border border-outline-variant text-secondary">
        No messages found.
    </div>
<?php endif; ?>
</div>

</div>
</main>
</body></html>

<?php
require_once 'includes/db.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "The email or password you entered is incorrect. Please try again.";
            }
        } else {
            $error = "The email or password you entered is incorrect. Please try again.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm'];

        if ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = "Email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $success = "Registration successful. You can now login.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Login / Register - LUXE</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#000000",
                        "on-primary": "#ffffff",
                        "surface": "#f7f9fb",
                        "on-surface": "#191c1e",
                        "surface-variant": "#e0e3e5",
                        "on-surface-variant": "#45464d",
                        "outline": "#76777d",
                        "outline-variant": "#c6c6cd",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "primary-container": "#131b2e",
                        "on-primary-container": "#7c839b",
                        "secondary": "#505f76"
                    }
                }
            }
        }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col antialiased selection:bg-primary selection:text-on-primary">
<header class="bg-surface flex justify-between items-center w-full px-8 h-16 sticky top-0 border-b border-outline-variant shadow-sm z-50">
<a class="text-2xl font-bold text-primary tracking-tight" href="index.php">LUXE</a>
<a class="text-secondary hover:text-primary transition-colors flex items-center gap-1 text-sm font-medium" href="index.php">
<span class="material-symbols-outlined text-[20px]">close</span>
<span class="hidden md:inline">Cancel</span>
</a>
</header>

<main class="flex-grow flex items-center justify-center p-4 md:p-8 bg-surface-container-low/50 relative overflow-hidden">
<div class="absolute inset-0 pointer-events-none overflow-hidden flex justify-center items-center opacity-[0.03]">
<span class="material-symbols-outlined text-[800px] text-primary" style="font-variation-settings: 'FILL' 1;">security</span>
</div>

<div class="w-full max-w-[480px] bg-surface-container-lowest rounded-lg shadow-lg border border-outline-variant relative z-10 flex flex-col">
<div class="flex border-b border-outline-variant w-full">
<button class="flex-1 py-4 text-center text-sm font-medium border-b-2 border-primary text-primary transition-colors hover:bg-surface-container-low" id="tab-login" onclick="switchTab('login')">Sign In</button>
<button class="flex-1 py-4 text-center text-sm font-medium border-b-2 border-transparent text-secondary hover:text-primary hover:bg-surface-container-low transition-colors" id="tab-register" onclick="switchTab('register')">Register</button>
</div>

<div class="p-8">
<?php if ($error): ?>
<div class="bg-error-container text-on-error-container p-3 rounded flex items-start gap-2 border border-error/20 mb-4">
<span class="material-symbols-outlined text-[20px] mt-0.5 text-error">error</span>
<p class="text-sm"><?= htmlspecialchars($error) ?></p>
</div>
<?php endif; ?>
<?php if ($success): ?>
<div class="bg-green-100 text-green-800 p-3 rounded flex items-start gap-2 border border-green-200 mb-4">
<span class="material-symbols-outlined text-[20px] mt-0.5 text-green-600">check_circle</span>
<p class="text-sm"><?= htmlspecialchars($success) ?></p>
</div>
<?php endif; ?>

<!-- Login Form -->
<form method="POST" action="login.php" class="flex flex-col gap-6 <?= isset($_POST['action']) && $_POST['action'] === 'register' && !$success ? 'hidden' : '' ?>" id="form-login">
<input type="hidden" name="action" value="login">
<div class="text-center mb-2">
<h1 class="text-xl font-semibold text-on-surface mb-1">Welcome Back</h1>
<p class="text-sm text-on-surface-variant">Access your professional account</p>
</div>

<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface-variant" for="login-email">Email Address</label>
<div class="relative flex items-center">
<span class="material-symbols-outlined absolute left-3 text-outline text-[20px]">mail</span>
<input name="email" class="w-full pl-10 pr-3 py-2 bg-transparent border border-outline-variant rounded focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm" id="login-email" placeholder="name@company.com" type="email" required/>
</div>
</div>

<div class="flex flex-col gap-2">
<div class="flex justify-between items-center">
<label class="text-xs font-semibold text-on-surface-variant" for="login-password">Password</label>
</div>
<div class="relative flex items-center">
<span class="material-symbols-outlined absolute left-3 text-outline text-[20px]">lock</span>
<input name="password" class="w-full pl-10 pr-10 py-2 bg-transparent border border-outline-variant rounded focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm" id="login-password" placeholder="••••••••" type="password" required/>
</div>
</div>

<button type="submit" class="w-full py-3 mt-4 bg-primary text-on-primary text-sm font-medium rounded shadow-sm hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
    Sign In
    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</form>

<!-- Register Form -->
<form method="POST" action="login.php" class="<?= isset($_POST['action']) && $_POST['action'] === 'register' && !$success ? 'flex' : 'hidden' ?> flex-col gap-4" id="form-register">
<input type="hidden" name="action" value="register">
<div class="text-center mb-2">
<h1 class="text-xl font-semibold text-on-surface mb-1">Create an Account</h1>
<p class="text-sm text-on-surface-variant">Join LUXE Professional today</p>
</div>

<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface-variant" for="reg-name">Full Name</label>
<input name="name" class="w-full px-3 py-2 bg-transparent border border-outline-variant rounded focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm" id="reg-name" placeholder="Jane Doe" type="text" required/>
</div>

<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface-variant" for="reg-email">Email Address</label>
<input name="email" class="w-full px-3 py-2 bg-transparent border border-outline-variant rounded focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm" id="reg-email" placeholder="name@company.com" type="email" required/>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface-variant" for="reg-password">Password</label>
<input name="password" class="w-full px-3 py-2 bg-transparent border border-outline-variant rounded focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm" id="reg-password" placeholder="••••••••" type="password" required/>
</div>
<div class="flex flex-col gap-2">
<label class="text-xs font-semibold text-on-surface-variant" for="reg-confirm">Confirm Password</label>
<input name="confirm" class="w-full px-3 py-2 bg-transparent border border-outline-variant rounded focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition-all text-sm" id="reg-confirm" placeholder="••••••••" type="password" required/>
</div>
</div>

<button type="submit" class="w-full py-3 mt-4 bg-primary text-on-primary text-sm font-medium rounded shadow-sm hover:opacity-90 active:scale-[0.98] transition-all flex justify-center items-center gap-2">
    Register Account
</button>
</form>

</div>
</div>
</main>
<script>
function switchTab(tab) {
    const loginForm = document.getElementById('form-login');
    const registerForm = document.getElementById('form-register');
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');

    if (tab === 'login') {
        loginForm.classList.remove('hidden');
        loginForm.classList.add('flex');
        registerForm.classList.add('hidden');
        registerForm.classList.remove('flex');
        
        tabLogin.classList.add('border-primary', 'text-primary');
        tabLogin.classList.remove('border-transparent', 'text-secondary');
        tabRegister.classList.remove('border-primary', 'text-primary');
        tabRegister.classList.add('border-transparent', 'text-secondary');
    } else {
        registerForm.classList.remove('hidden');
        registerForm.classList.add('flex');
        loginForm.classList.add('hidden');
        loginForm.classList.remove('flex');

        tabRegister.classList.add('border-primary', 'text-primary');
        tabRegister.classList.remove('border-transparent', 'text-secondary');
        tabLogin.classList.remove('border-primary', 'text-primary');
        tabLogin.classList.add('border-transparent', 'text-secondary');
    }
}
</script>
</body></html>

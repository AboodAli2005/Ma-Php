<?php
require_once 'includes/db.php';
session_start();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Please fill out all required fields.';
    } else {
        $full_message = "Subject: $subject\n\n$message";
        
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $full_message);
        
        if ($stmt->execute()) {
            $success_message = "Your message has been sent successfully. We will get back to you shortly.";
        } else {
            $error_message = "Failed to send message. Please try again later.";
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow py-xl px-margin-mobile md:px-margin-desktop w-full max-w-container-max mx-auto">
<div class="text-center mb-xl">
<h1 class="font-headline-xl text-headline-xl text-primary mb-4">Get in Touch</h1>
<p class="font-body-lg text-body-lg text-secondary max-w-2xl mx-auto">We're here to help and answer any question you might have. We look forward to hearing from you.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Contact Info Panel -->
<div class="lg:col-span-4 space-y-md">
<div class="bg-surface-container-lowest rounded-lg p-md shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant/30 flex items-start gap-4 hover:shadow-[0px_6px_24px_rgba(15,23,42,0.08)] transition-shadow">
<div class="text-on-tertiary-container mt-1">
<span class="material-symbols-outlined" data-icon="location_on" data-weight="fill" style="font-variation-settings: 'FILL' 1;">location_on</span>
</div>
<div>
<h3 class="font-label-md text-label-md text-primary mb-1">Address</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">123 Luxury Avenue, Suite 500<br/>Gaza, NY 10001</p>
</div>
</div>
<div class="bg-surface-container-lowest rounded-lg p-md shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant/30 flex items-start gap-4 hover:shadow-[0px_6px_24px_rgba(15,23,42,0.08)] transition-shadow">
<div class="text-on-tertiary-container mt-1">
<span class="material-symbols-outlined" data-icon="phone" data-weight="fill" style="font-variation-settings: 'FILL' 1;">phone</span>
</div>
<div>
<h3 class="font-label-md text-label-md text-primary mb-1">Phone</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">+970567677406</p>
</div>
</div>
<div class="bg-surface-container-lowest rounded-lg p-md shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant/30 flex items-start gap-4 hover:shadow-[0px_6px_24px_rgba(15,23,42,0.08)] transition-shadow">
<div class="text-on-tertiary-container mt-1">
<span class="material-symbols-outlined" data-icon="mail" data-weight="fill" style="font-variation-settings: 'FILL' 1;">mail</span>
</div>
<div>
<h3 class="font-label-md text-label-md text-primary mb-1">Email</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">contact@luxe-professional.com</p>
</div>
</div>
<div class="bg-surface-container-lowest rounded-lg p-md shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant/30 flex items-start gap-4 hover:shadow-[0px_6px_24px_rgba(15,23,42,0.08)] transition-shadow">
<div class="text-on-tertiary-container mt-1">
<span class="material-symbols-outlined" data-icon="schedule" data-weight="fill" style="font-variation-settings: 'FILL' 1;">schedule</span>
</div>
<div>
<h3 class="font-label-md text-label-md text-primary mb-1">Working Hours</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Mon - Fri: 9:00 AM - 6:00 PM<br/>Sat - Sun: Closed</p>
</div>
</div>
</div>
<!-- Contact Form -->
<div class="lg:col-span-8">
<div class="bg-surface-container-lowest rounded-lg p-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant/30">

<?php if ($success_message): ?>
    <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>
<?php if ($error_message): ?>
    <div class="bg-error-container text-on-error-container p-4 rounded mb-6 border border-error/20">
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php endif; ?>

<form action="contact.php" method="POST" class="space-y-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="block font-label-sm text-label-sm text-primary mb-2" for="name">Name <span class="text-error">*</span></label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded-md px-4 py-3 font-body-md text-body-md text-primary focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all outline-none" id="name" name="name" placeholder="Jane Doe" type="text" required/>
</div>
<div>
<label class="block font-label-sm text-label-sm text-primary mb-2" for="email">Email <span class="text-error">*</span></label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded-md px-4 py-3 font-body-md text-body-md text-primary focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all outline-none" id="email" name="email" placeholder="jane@example.com" type="email" required/>
</div>
</div>
<div>
<label class="block font-label-sm text-label-sm text-primary mb-2" for="subject">Subject</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded-md px-4 py-3 font-body-md text-body-md text-primary focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all outline-none" id="subject" name="subject" placeholder="How can we help?" type="text"/>
</div>
<div>
<label class="block font-label-sm text-label-sm text-primary mb-2" for="message">Message <span class="text-error">*</span></label>
<textarea class="w-full bg-surface-container-lowest border border-outline-variant rounded-md px-4 py-3 font-body-md text-body-md text-primary focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all outline-none resize-y" id="message" name="message" placeholder="Your message here..." rows="5" required></textarea>
</div>
<div>
<button class="w-full md:w-auto bg-primary text-on-primary font-label-md text-label-md px-8 py-3 rounded-md hover:opacity-90 transition-opacity active:scale-[0.98] cursor-pointer border-none" type="submit">
                                Send Message
                            </button>
</div>
</form>
</div>
</div>
</div>
</main>

<?php require_once 'includes/footer.php'; ?>

<nav class="hidden md:flex fixed left-0 top-0 h-full w-64 border-r border-outline-variant bg-surface-container-low flex flex-col py-md z-50">
<!-- Header area -->
<div class="px-md pb-md mb-sm flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-headline-sm text-headline-sm uppercase overflow-hidden" title="Admin Profile">
    <?= substr($_SESSION['user_name'] ?? 'A', 0, 1) ?>
</div>
<div>
<div class="font-headline-sm text-headline-sm font-bold text-primary">Admin Panel</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Management Console</div>
</div>
</div>
<div class="px-4 mb-md">
<a href="products.php" class="w-full bg-on-tertiary-container text-white rounded-lg py-2 font-label-md text-label-md flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-symbols-outlined text-[18px]">add</span>
    New Product
</a>
</div>
<!-- Navigation Links -->
<div class="flex-1 overflow-y-auto flex flex-col gap-1 font-label-md text-label-md">
<a class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-container-high' ?> rounded-lg mx-2 flex items-center gap-3 px-4 py-3 transition-transform active:scale-95" href="index.php">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
    Dashboard
</a>
<a class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-container-high' ?> mx-2 rounded-lg flex items-center gap-3 px-4 py-3 transition-transform active:scale-95 transition-all" href="orders.php">
<span class="material-symbols-outlined">shopping_cart</span>
    Orders
</a>
<a class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-container-high' ?> mx-2 rounded-lg flex items-center gap-3 px-4 py-3 transition-transform active:scale-95 transition-all" href="products.php">
<span class="material-symbols-outlined">inventory_2</span>
    Products
</a>
<a class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-container-high' ?> mx-2 rounded-lg flex items-center gap-3 px-4 py-3 transition-transform active:scale-95 transition-all" href="users.php">
<span class="material-symbols-outlined">group</span>
    Users
</a>
<a class="<?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-container-high' ?> mx-2 rounded-lg flex items-center gap-3 px-4 py-3 transition-transform active:scale-95 transition-all" href="messages.php">
<span class="material-symbols-outlined">forum</span>
    Messages
</a>
</div>
<!-- Footer Links -->
<div class="mt-auto pt-md flex flex-col gap-1 font-label-md text-label-md border-t border-outline-variant mx-4">
<a class="text-on-surface-variant hover:bg-surface-container-high -mx-2 rounded-lg flex items-center gap-3 px-4 py-3 transition-transform active:scale-95 transition-all" href="../index.php">
<span class="material-symbols-outlined">storefront</span>
    Storefront
</a>
<a class="text-on-surface-variant hover:bg-surface-container-high -mx-2 rounded-lg flex items-center gap-3 px-4 py-3 transition-transform active:scale-95 transition-all" href="../logout.php">
<span class="material-symbols-outlined">logout</span>
    Logout
</a>
</div>
</nav>

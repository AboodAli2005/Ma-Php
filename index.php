<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Fetch some featured products
$query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($query);
?>

<!-- Hero Section -->
<section class="relative h-[600px] flex items-center justify-center bg-surface-container-low">
<div class="absolute inset-0 z-0">
<img alt="Hero background" class="w-full h-full object-cover opacity-60" data-alt="A sophisticated, modern corporate interior with ample whitespace, clean lines, and subtle warm lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAXAMY7lcU2jtnhPt7r4pyy7yuTJssCXoA-Px_Np4nxwwUz6H0qfPXLzUx6_TJhQCz30_PtwDJJvZI8-CgYiztwyQ5f-9cHrOm0QdpP9WVeKinitj7ZnNk342sutrc0XwtAZcyCY1PTofAWn9ta5-1z74rEa8wofZPjoYHQlnaFkFzRpU98VVhx8QvyUd17v9JVnoszzq8E8atLNC2YB0vBskZBEGQFX_uc5cZ1oc_C5RI-nTvwYbW1wQ"/>
</div>
<div class="relative z-10 text-center max-w-2xl px-margin-mobile">
<h1 class="font-headline-xl text-headline-xl mb-4 text-primary">Welcome to LUXE</h1>
<p class="font-body-lg text-body-lg mb-8 text-on-surface-variant">Elevate your professional standards with our curated selection of premium tools and accessories.</p>
<a href="products.php" class="bg-on-tertiary-container hover:bg-on-tertiary-container/90 text-on-primary font-label-md text-label-md px-8 py-3 rounded transition-colors shadow-sm inline-block">Shop Now</a>
</div>
</section>
<!-- Featured Products -->
<section class="py-xl px-margin-desktop max-w-container-max mx-auto">
<h2 class="font-headline-lg text-headline-lg text-center mb-lg">Featured Products</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
<?php
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        ?>
        <div class="bg-surface-lowest rounded-lg p-md shadow-[0px_4px_20px_rgba(15,23,42,0.05)] hover:shadow-[0px_8px_30px_rgba(15,23,42,0.08)] transition-shadow duration-300 group flex flex-col h-full border border-outline-variant/30">
        <a href="product.php?id=<?= $row['id'] ?>" class="aspect-square mb-4 overflow-hidden rounded bg-surface-container-low flex items-center justify-center">
        <img alt="<?= htmlspecialchars($row['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="<?= htmlspecialchars($row['image_url']) ?>"/>
        </a>
        <div class="flex-grow flex flex-col">
        <h3 class="font-headline-sm text-headline-sm mb-1 text-primary"><?= htmlspecialchars($row['name']) ?></h3>
        <p class="font-body-sm text-body-sm text-on-surface-variant mb-4 line-clamp-2"><?= htmlspecialchars($row['description']) ?></p>
        <div class="mt-auto flex items-center justify-between">
        <span class="font-label-md text-label-md text-primary">$<?= number_format($row['price'], 2) ?></span>
        <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <button type="submit" aria-label="Add to cart" class="text-on-tertiary-container hover:bg-surface-container rounded-full p-2 transition-colors">
            <span class="material-symbols-outlined">add_shopping_cart</span>
            </button>
        </form>
        </div>
        </div>
        </div>
        <?php
    }
} else {
    echo "<p class='col-span-full text-center'>No products found.</p>";
}
?>
</div>
</section>

<?php require_once 'includes/footer.php'; ?>

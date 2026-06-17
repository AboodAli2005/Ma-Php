<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Categories query
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// Products query based on filters
$where = [];
$params = [];
$types = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $where[] = "p.name LIKE ?";
    $params[] = '%' . trim($_GET['search']) . '%';
    $types .= 's';
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $where[] = "p.category_id = ?";
    $params[] = $_GET['category'];
    $types .= 'i';
}

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
if (count($where) > 0) {
    $query .= " WHERE " . implode(" AND ", $where);
}

// Sorting
$sort = $_GET['sort'] ?? '';
if ($sort === 'price_asc') {
    $query .= " ORDER BY p.price ASC";
} elseif ($sort === 'price_desc') {
    $query .= " ORDER BY p.price DESC";
} else {
    $query .= " ORDER BY p.created_at DESC";
}

$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products_result = $stmt->get_result();
?>

<!-- Main Content Canvas -->
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-lg grid grid-cols-1 md:grid-cols-12 gap-gutter">
<!-- Filter Sidebar -->
<aside class="md:col-span-3 space-y-md">
<div class="bg-surface-container-lowest p-md rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-outline-variant">
<h3 class="font-headline-sm text-headline-sm mb-sm text-primary">Filters</h3>
<form action="products.php" method="GET">
<?php if(isset($_GET['search'])): ?>
<input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
<?php endif; ?>

<div class="mb-md">
<h4 class="font-label-md text-label-md mb-xs text-secondary">Category</h4>
<div class="space-y-2">
<label class="flex items-center gap-2 cursor-pointer">
<input type="radio" name="category" value="" class="rounded border-outline-variant text-primary focus:ring-primary focus:ring-opacity-10 focus:ring-2" onchange="this.form.submit()" <?= empty($_GET['category']) ? 'checked' : '' ?>/>
<span class="font-body-sm text-body-sm text-on-surface-variant">All Categories</span>
</label>
<?php while($cat = $categories_result->fetch_assoc()): ?>
<label class="flex items-center gap-2 cursor-pointer">
<input type="radio" name="category" value="<?= $cat['id'] ?>" class="rounded border-outline-variant text-primary focus:ring-primary focus:ring-opacity-10 focus:ring-2" onchange="this.form.submit()" <?= isset($_GET['category']) && $_GET['category'] == $cat['id'] ? 'checked' : '' ?>/>
<span class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($cat['name']) ?></span>
</label>
<?php endwhile; ?>
</div>
</div>
<div>
<h4 class="font-label-md text-label-md mb-xs text-secondary">Sort By</h4>
<select name="sort" onchange="this.form.submit()" class="w-full border-outline-variant rounded-md font-body-sm text-body-sm text-on-surface focus:border-primary focus:ring-primary focus:ring-opacity-10 focus:ring-2 bg-transparent border p-2">
<option value="">Recommended</option>
<option value="price_asc" <?= isset($_GET['sort']) && $_GET['sort'] === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
<option value="price_desc" <?= isset($_GET['sort']) && $_GET['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
</select>
</div>
</form>
</div>
</aside>

<!-- Product Grid -->
<section class="md:col-span-9">
<?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
    <h2 class="text-xl mb-4 text-primary">Search results for: "<?= htmlspecialchars($_GET['search']) ?>"</h2>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter">
<?php if ($products_result && $products_result->num_rows > 0): ?>
    <?php while($row = $products_result->fetch_assoc()): ?>
    <article class="bg-surface-container-lowest rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] hover:shadow-[0px_8px_30px_rgba(15,23,42,0.08)] transition-all duration-300 border border-outline-variant group flex flex-col h-full overflow-hidden">
    <a href="product.php?id=<?= $row['id'] ?>" class="relative w-full h-48 bg-surface-container overflow-hidden block">
    <img alt="<?= htmlspecialchars($row['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="<?= htmlspecialchars($row['image_url']) ?>"/>
    </a>
    <div class="p-md flex flex-col flex-grow">
    <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider mb-base"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></span>
    <h3 class="font-headline-sm text-headline-sm text-primary mb-xs"><?= htmlspecialchars($row['name']) ?></h3>
    <p class="font-body-lg text-body-lg text-on-surface-variant mb-md font-medium">$<?= number_format($row['price'], 2) ?></p>
    <div class="mt-auto flex gap-xs">
    <a href="product.php?id=<?= $row['id'] ?>" class="flex-1 bg-surface-container-lowest text-primary border border-outline-variant font-label-md text-label-md py-2 px-4 rounded hover:bg-surface-container-low transition-colors text-center inline-block">View Details</a>
    <form action="cart.php" method="POST" class="flex-1">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
        <button type="submit" class="w-full bg-[#131b2e] text-on-primary font-label-md text-label-md py-2 px-4 rounded hover:opacity-90 transition-opacity text-center">Add to Cart</button>
    </form>
    </div>
    </div>
    </article>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-span-full py-12 text-center text-on-surface-variant">
        No products found matching your criteria.
    </div>
<?php endif; ?>
</div>
</section>
</main>

<?php require_once 'includes/footer.php'; ?>

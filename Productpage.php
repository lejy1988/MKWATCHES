<?php
require_once 'inc/config.php'; // make sure path is correct

// Get category from URL, default to 'all'
$category = $_GET['category'] ?? 'all';

// Fetch products
try {
    if ($category === 'all') {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->execute([$category]);
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MK Watches - Products</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
    body { background-color: #1b1a1a; color: #fff; }
    .card-img-top { height: 250px; object-fit: cover; }
    .card { background-color: #2a2a2a; }
</style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Our Collection</h1>

    <!-- Filter links -->
    <div class="text-center mb-4">
        <a href="?category=all" class="btn btn-outline-light mx-1">All</a>
        <a href="?category=men" class="btn btn-outline-light mx-1">Men</a>
        <a href="?category=women" class="btn btn-outline-light mx-1">Women</a>
        <a href="?category=kids" class="btn btn-outline-light mx-1">Kids</a>
    </div>

    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                            <p class="card-text">£<?= number_format($p['price'], 2) ?></p>
                            <p class="card-text"><small><?= htmlspecialchars($p['category']) ?></small></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">No products found in this category.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

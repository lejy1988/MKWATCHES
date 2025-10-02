<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';

require_login();

// Get product ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Optionally, delete image from uploads folder
$stmt = $pdo->prepare('SELECT image FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if ($product && $product['image']) {
    $file = __DIR__ . '/../uploads/' . $product['image'];
    if (file_exists($file)) {
        unlink($file);
    }
}

// Delete product
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([$id]);

header('Location: index.php');
exit;

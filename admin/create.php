<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';
require_login();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? 'Mens';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf($token)) {
        $error = 'Invalid CSRF token.';
    } elseif (empty($name) || empty($price)) {
        $error = 'Name and Price are required.';
    } else {
        try {
            $image = handle_image_upload($_FILES['image'] ?? []);
            $slug = slugify($name);

            $stmt = $pdo->prepare('INSERT INTO products (name, slug, description, price, category, image) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $slug, $description, $price, $category, $image]);

            $success = 'Product added successfully!';
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$csrf_token = csrf_token();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Add Product | MK Watches</title>
<link rel="icon" href="../assets/anotherbanner.png" type="image/png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Roboto', sans-serif; background-color: #1b1a1a; padding-top: 70px; }
    .navbar { background-color: rgba(78,78,78,0.8); }
    .cart-btn { background-color: black; color: white; border: black; }
    .card-container { background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; }
    footer { background-color: #212121; color: #fff; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <a class="navbar-brand" href="../index.php">
      <img src="../anotherbanner.png" width="30" height="30" class="d-inline-block align-top" alt=""> MK Watches
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
          aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mr-auto">
          <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Admin</a>
              <div class="dropdown-menu">
                  <a class="dropdown-item" href="create.php">Add</a>
                  <a class="dropdown-item" href="edit.php">Edit</a>
                  <a class="dropdown-item" href="delete.php">Delete</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="logout.php">Log out</a>
              </div>
          </li>
      </ul>
  </div>
</nav>

<!-- Add Product Container -->
<div class="container mt-4">
    <div class="card-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New Product</h2>
            <a href="index.php" class="btn btn-secondary">Back to Products</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="Mens">Mens</option>
                    <option value="Ladies">Ladies</option>
                    <option value="Children">Children</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Product</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="py-4 mt-5">
    <div class="container text-center">
        <p class="mb-2">&copy; 2025 MK Watches. Admin Portal.</p>
        <p class="text-muted">For internal use only | Unauthorized use will be prosecuted</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

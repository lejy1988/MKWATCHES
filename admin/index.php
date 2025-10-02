<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';
require_login();

$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Admin Dashboard | MK Watches</title>
<link rel="icon" href="../assets/anotherbanner.png" type="image/png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Roboto', sans-serif; background-color: #1b1a1a; padding-top: 70px; }
    .navbar { background-color: rgba(78,78,78,0.8); }
    .cart-btn { background-color: black; color: white; border: black; }
    .table img { max-width: 50px; }
    .card-container { background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; }
    footer { background-color: #212121; color: #fff; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <a class="navbar-brand" href="../index.html">
      <img src="../anotherbanner.png" width="30" height="30" class="d-inline-block align-top" alt=""> MK Watches
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
          aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mr-auto">
          <li class="nav-item"><a class="nav-link" href="../index.html">Home</a></li>
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

<!-- Dashboard Container -->
<div class="container mt-4">
    <div class="card-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Products</h2>
            <div>
                <a href="create.php" class="btn btn-success">Add New Product</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="6" class="text-center">No products found.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= $product['category'] ?></td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <?php if ($product['image']): ?>
                                    <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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

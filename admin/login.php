<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf($token)) {
        $error = 'Invalid CSRF token.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$csrf_token = csrf_token();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | MK Watches</title>
    <link rel="icon" href="../assets/anotherbanner.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>

<body style="background-color: #1b1a1a; font-family: 'Roboto', sans-serif;">

    
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


    <!-- Login Section -->
    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
        <div class="row w-100">
            <!-- Optional Banner (hidden on mobile) -->
            <div class="col-md-6 d-none d-md-flex justify-content-center align-items-center">
                <img src="../WACTHREGISTER.jpg" alt="Admin Banner" class="img-fluid" style="max-width: 100%; border-radius: 8px;">
            </div>

            <!-- Login Form -->
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <form method="post" style="background-color: #f8f9fa; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; max-width: 400px;">
                    <h1 class="text-center">Admin Login</h1>
                    <p class="text-center">Restricted access — staff only</p>
                    <hr>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="mb-3">
                        <label for="username">Admin Username:</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter admin username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Login</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>

                    <hr>
                    <p class="text-center"><strong>Note:</strong> Unauthorized access prohibited.</p>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-2">&copy; 2025 MK Watches. Admin Portal.</p>
            <p class="text-muted">For internal use only | Unauthorized use will be prosecuted</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

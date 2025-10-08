<?php
require_once 'inc/config.php'; // $pdo already defined and session started

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch logged-in user info
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch products
$stmt2 = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MK Watches - Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
    body { background-color: #1b1a1a; color: #fff; }
    .card { background-color: #2a2a2a; color: #fff; }
    .card-img-top { height: 250px; object-fit: cover; }
    .container { margin-top: 80px; }
</style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-grey fixed-top" style="background-color: rgba(78,78,78,0.8);">
    <a class="navbar-brand" href="dashboard.php" style="color: white;">
        <img src="anotherbanner.png" width="30" height="30" class="d-inline-block align-top" alt="">
        MK Watches
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left menu -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php" style="color: white;">Home <span class="sr-only">(current)</span></a>
            </li>

            <!-- The Collection dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="collectionDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white;">
                    The Collection
                </a>
                <div class="dropdown-menu" aria-labelledby="collectionDropdown">
                    <a class="dropdown-item" href="adminproduct.php?category=men">Mens</a>
                    <a class="dropdown-item" href="adminproduct.php?category=women">Ladies</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="adminproduct.php?category=kids">Children</a>
                </div>
            </li>
        </ul>

        <!-- Right menu -->
        <ul class="navbar-nav ml-auto align-items-center">
            <!-- User greeting -->
            <li class="nav-item mr-3">
                <span class="navbar-text" style="color: white;">👋 <?= htmlspecialchars($user['name']); ?></span>
            </li>

            <!-- Logout button -->
            <li class="nav-item mr-3">
                <a class="btn btn-outline-light" href="logout_user.php">Logout</a>
            </li>

            <!-- Cart dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="cartDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: white;">
                    <img src="shopping-cart.png" width="30" height="30" class="d-inline-block align-top" alt="">
                    Cart (<span id="cart-count">0</span>)
                </a>
                <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="cartDropdown" style="min-width: 250px;">
                    <div id="cart-items">No items in cart</div>
                    <div class="dropdown-divider"></div>
                    <strong>Total: £<span id="cart-total">0.00</span></strong>
                    <a href="cart.html">
                        <button class="btn btn-dark mt-2" style="width: 100%;">Check out</button>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <h2 style="background-color: white; color: black" class="text-center my-4">
        👋 Welcome, <?= htmlspecialchars($user['name']); ?>!
    </h2>

    <h3 class="mb-4">Our Collection</h3>
    <div class="row">
        <?php foreach ($products as $p): ?>
            <div class="col-md-4 mb-4 product" data-category="<?= htmlspecialchars($p['category']); ?>">
                <div class="card">
                    <img src="uploads/<?= htmlspecialchars($p['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($p['name']); ?></h5>
                        <p class="card-text">£<?= number_format($p['price'], 2); ?></p>
                        <p class="card-text"><small><?= htmlspecialchars($p['category']); ?></small></p>

                        <!-- Add to Cart button -->
                        <button class="btn btn-dark add-to-cart" 
                            data-title="<?= htmlspecialchars($p['name']); ?>"
                            data-price="<?= number_format($p['price'], 2); ?>">
                            Add to Cart
                        </button>

                        <!-- View Details button -->
                        <button class="btn btn-outline-light view-details-btn mt-2"
                            data-title="<?= htmlspecialchars($p['name']); ?>"
                            data-price="<?= number_format($p['price'], 2); ?>"
                            data-description="<?= htmlspecialchars($p['description'] ?? 'No description available'); ?>"
                            data-img="uploads/<?= htmlspecialchars($p['image']); ?>">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Product Details Modal -->
<div id="modalOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.6);z-index:1040;"></div>

<div id="detailsModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
    background-color:#2a2a2a;color:#fff;padding:20px;border-radius:8px;z-index:1050;max-width:500px;width:90%;">
    <div id="modalContent"></div>
    <button id="closeModalBtn" style="margin-top:10px;padding:5px 10px;background-color:#fff;color:#000;border:none;border-radius:4px;cursor:pointer;">Close</button>
</div>
<div class="toast fade hide" id="cartToast" role="alert" aria-live="assertive" aria-atomic="true"
     style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
  <div class="toast-header bg-success text-white">
    <strong class="mr-auto">Cart</strong>
    <button type="button" class="close text-white" data-dismiss="toast">&times;</button>
  </div>
  <div class="toast-body">✅ Item added to cart!</div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = JSON.parse(localStorage.getItem('mkCart')) || [];

function updateCartUI() {
    const cartCount = cart.length;
    const cartItems = cart.map(item => `<div>${item.title} - £${item.price.toFixed(2)}</div>`).join('');
    
    // Update cart count
    document.getElementById('cart-count').textContent = cartCount;
    
    // Update cart items
    document.getElementById('cart-items').innerHTML = cartItems || 'No items in cart';
    
    // Update cart total
    const total = cart.reduce((sum, item) => sum + item.price, 0);
    document.getElementById('cart-total').textContent = total.toFixed(2);
}

updateCartUI();


// Add to Cart
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
        const title = btn.dataset.title;
        const price = parseFloat(btn.dataset.price);
        cart.push({title, price});
        localStorage.setItem('mkCart', JSON.stringify(cart));
        updateCartUI();
        $('#cartToast').toast({delay: 2000}).toast('show');
    });
});

// Modal
const modal = document.getElementById('detailsModal');
const overlay = document.getElementById('modalOverlay');
const modalContent = document.getElementById('modalContent');
const closeModalBtn = document.getElementById('closeModalBtn');

function showDetails(title, price, desc, img) {
    modalContent.innerHTML = `<img src="${img}" style="width:100%;border-radius:8px;margin-bottom:10px;">
    <h3>${title}</h3><p>Price: £${parseFloat(price).toFixed(2)}</p><p>${desc}</p>`;
    modal.style.display = 'block';
    overlay.style.display = 'block';
}

function closeModal() { modal.style.display = 'none'; overlay.style.display = 'none'; }

closeModalBtn.addEventListener('click', closeModal);
overlay.addEventListener('click', closeModal);

document.querySelectorAll('.view-details-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        showDetails(btn.dataset.title, btn.dataset.price, btn.dataset.description, btn.dataset.img);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const products = document.querySelectorAll('.product');

    function filterProducts(category) {
      category = category.toLowerCase();
        products.forEach(product => {
            const productCategory = product.getAttribute('data-category').toLowerCase();
            if (category === 'all' || productCategory === category) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const category = button.getAttribute('data-category');
            filterProducts(category);
        });
    });

    // Optional: filter based on URL param on page load
    const urlParams = new URLSearchParams(window.location.search);
    const initialCategory = urlParams.get('category') || 'all';
    filterProducts(initialCategory);
});
</script>


</body>
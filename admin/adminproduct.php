<?php
// admin/adminproduct.php

require_once __DIR__ . '/../inc/config.php'; 
require_once __DIR__ . '/../inc/functions.php';
require_login();

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
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
<title>Admin Product Page | MK Watches</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
    body { background-color: #1b1a1a; color: #fff; }
    .card { background-color: #2a2a2a; color: #fff; }
    .card-img-top { height: 250px; object-fit: cover; }
    .container { margin-top: 80px; }
    #modalOverlay {
        display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.5); z-index: 9998;
    }
    #detailsModal {
        display: none; position: fixed; top: 50%; left: 50%;
        transform: translate(-50%, -50%); background: white; padding: 20px;
        max-width: 400px; box-shadow: 0 0 15px rgba(0,0,0,0.3);
        z-index: 9999; border-radius: 8px; color: #000;
    }
    #detailsModal button { float: right; background: transparent; border: none; font-size: 20px; cursor: pointer; }
</style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Product Shopfront (Admin Test)</h1>

    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-md-4 mb-4 product" data-category="<?= htmlspecialchars($p['category']) ?>">
                    <div class="card">
                        <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                            <p class="card-text">£<?= number_format($p['price'], 2) ?></p>
                            <p class="card-text"><small><?= htmlspecialchars($p['category']) ?></small></p>
                            <button class="btn btn-dark view-details-btn" 
                                data-title="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= number_format($p['price'], 2) ?>"
                                data-description="<?= htmlspecialchars($p['description']) ?>"
                                data-img="../uploads/<?= htmlspecialchars($p['image']) ?>">
                                View Details
                            </button>
                            <button class="btn btn-light mt-2 add-to-cart" 
                                data-title="<?= htmlspecialchars($p['name']) ?>"
                                data-price="<?= number_format($p['price'], 2) ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12"><p class="text-center">No products found.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="modalOverlay"></div>
<div id="detailsModal">
    <button id="closeModalBtn">&times;</button>
    <div id="modalContent"></div>
</div>

<!-- Toast -->
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
    console.log("Cart:", cart);
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
</body>
</html>

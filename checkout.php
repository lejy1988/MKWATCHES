<?php
require_once 'inc/config.php';
 // $pdo + session started

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Check if cart data was posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    $cartItems = json_decode($_POST['cart'], true);

    if (!$cartItems || count($cartItems) === 0) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Calculate total
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * ($item['quantity'] ?? 1);
        }

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount) VALUES (?, NOW(), ?)");
        $stmt->execute([$userId, $totalAmount]);
        $orderId = $pdo->lastInsertId();

        // Insert order items
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmtItem->execute([$orderId, $item['id'], $item['quantity'] ?? 1, $item['price']]);
        }

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

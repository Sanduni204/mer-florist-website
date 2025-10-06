<?php
session_start();
require "Config/config.php";

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add') {
        $item_id = (int)$_POST['item_id'];
        $quantity = 1; // Default quantity
        
        // Fetch item details from database
        $stmt = $conn->prepare("SELECT * FROM shop WHERE fid = :id");
        $stmt->execute([':id' => $item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($item) {
            // Check if item already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['id'] == $item_id) {
                    $cart_item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $item_id,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => $quantity
                ];
            }
            
            // Return cart count
            $cart_count = count($_SESSION['cart']);
            echo json_encode(['success' => true, 'cart_count' => $cart_count, 'message' => 'Item added to cart!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
    } elseif ($action === 'clear_cart') {
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
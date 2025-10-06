<?php require "includes/header.php"; ?>
<?php require "Config/config.php"; ?>
<?php
// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'add':
                $item_id = (int)$_POST['item_id'];
                $quantity = (int)($_POST['quantity'] ?? 1);
                
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
                }
                break;
                
            case 'remove':
                $item_id = (int)$_POST['item_id'];
                $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($item_id) {
                    return $item['id'] != $item_id;
                });
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                break;
                
            case 'update':
                $item_id = (int)$_POST['item_id'];
                $quantity = (int)$_POST['quantity'];
                
                if ($quantity <= 0) {
                    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($item_id) {
                        return $item['id'] != $item_id;
                    });
                    $_SESSION['cart'] = array_values($_SESSION['cart']);
                } else {
                    foreach ($_SESSION['cart'] as &$cart_item) {
                        if ($cart_item['id'] == $item_id) {
                            $cart_item['quantity'] = $quantity;
                            break;
                        }
                    }
                }
                break;
                
            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
    }
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>

<style>
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.cart-title {
    text-align: center;
    color: #2c3e50;
    font-size: 2.5rem;
    margin-bottom: 30px;
    font-style: italic;
    position: relative;
}

.cart-title::after {
    content: "";
    display: block;
    width: 80px;
    height: 3px;
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    margin: 15px auto 0;
    border-radius: 2px;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 2px solid rgba(237, 119, 135, 0.2);
    border-radius: 15px;
    margin-bottom: 15px;
    background: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.cart-item-image {
    width: 100px;
    height: 100px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 20px;
}

.cart-item-details {
    flex: 1;
}

.cart-item-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.cart-item-price {
    color: #ed7787;
    font-size: 1.1rem;
    font-weight: 500;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-input {
    width: 60px;
    padding: 8px;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
}

.cart-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.update-btn {
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    color: white;
}

.remove-btn {
    background: #e74c3c;
    color: white;
}

.cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.cart-summary {
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-top: 30px;
    text-align: center;
}

.cart-total {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.checkout-btn {
    background: white;
    color: #ed7787;
    padding: 15px 40px;
    border: none;
    border-radius: 25px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin: 0 10px;
}

.checkout-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.empty-cart {
    text-align: center;
    padding: 50px 20px;
    color: #666;
}

.empty-cart i {
    font-size: 4rem;
    color: #ed7787;
    margin-bottom: 20px;
}

.continue-shopping {
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.continue-shopping:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(237, 119, 135, 0.3);
}
</style>

<div class="cart-container">
    <h1 class="cart-title">Shopping Cart</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your cart is empty</h3>
            <p>Add some beautiful flowers to your cart to get started!</p>
            <a href="<?php echo APPURL; ?>1catalogue.php" class="continue-shopping">
                <i class="fas fa-leaf"></i> Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <div class="cart-item">
                <?php if ($item['image']): ?>
                    <img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                <?php else: ?>
                    <div class="cart-item-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">
                        No Image
                    </div>
                <?php endif; ?>
                
                <div class="cart-item-details">
                    <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="cart-item-price">RS. <?php echo number_format($item['price'], 2); ?></div>
                </div>
                
                <div class="cart-item-actions">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                               min="1" class="quantity-input">
                        <button type="submit" class="cart-btn update-btn">Update</button>
                    </form>
                    
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="cart-btn remove-btn">Remove</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="cart-summary">
            <div class="cart-total">
                Total: RS. <?php echo number_format($cart_total, 2); ?>
            </div>
            
            <a href="<?php echo APPURL; ?>1payment.php?cart=1" class="checkout-btn">
                <i class="fas fa-credit-card"></i> Proceed to Checkout
            </a>
            
            <a href="<?php echo APPURL; ?>1catalogue.php" class="checkout-btn">
                <i class="fas fa-leaf"></i> Continue Shopping
            </a>
            
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="checkout-btn" style="background: #e74c3c; color: white;">
                    <i class="fas fa-trash"></i> Clear Cart
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require "includes/footer.php"; ?>
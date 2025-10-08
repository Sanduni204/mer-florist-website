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
                            'description' => $item['description'] ?? '',
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
    display: flex;
    gap: 30px;
}

.cart-main-content {
    flex: 2;
}

.cart-sidebar {
    flex: 1;
    position: sticky;
    top: 90px;
    height: fit-content;
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
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 500;
}

.cart-item-description {
    color: #7f8c8d;
    font-size: 0.9rem;
    font-style: italic;
    margin-top: 5px;
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

/* Show spinner arrows always visible */
.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: auto;
    opacity: 1;
    cursor: pointer;
}

/* Firefox spinner buttons */
.quantity-input[type=number] {
    -moz-appearance: textfield;
}

/* Custom styling for better visibility */
.quantity-input:hover::-webkit-outer-spin-button,
.quantity-input:hover::-webkit-inner-spin-button {
    opacity: 1;
}

.quantity-input:focus {
    border-color: #ed7787;
    outline: none;
    box-shadow: 0 0 8px rgba(237, 119, 135, 0.3);
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
    background: transparent;
    color: #ed7787;
    border: 2px solid #ed7787;
}

.remove-btn {
    background: transparent;
    color: #e74c3c;
    border: 2px solid #e74c3c;
}

.cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.cart-summary {
    background: white;
    color: #2c3e50;
    padding: 25px;
    border-radius: 15px;
    margin: 0;
    border: 3px solid white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 100%;
}

.cart-total {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: #2c3e50;
    line-height: 1.2;
}

.checkout-btn {
    background: white;
    color: #b6b8bc;
    padding: 10px 20px;
    border: 2px solid #b6b8bc;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    margin: 10px 0;
    width: 100%;
    box-sizing: border-box;
}

.checkout-btn:hover {
    background: #b6b8bc;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(182, 184, 188, 0.3);
}

.checkout-btn.clear-cart {
    background: white;
    color: #e74c3c;
    border-color: #e74c3c;
}

.checkout-btn.clear-cart:hover {
    background: #e74c3c;
    color: white;
}

.empty-cart {
    text-align: center;
    padding: 50px 20px;
    color: #666;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
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

/* Responsive Design */
@media (max-width: 1024px) {
    .cart-container {
        gap: 20px;
    }
    
    .cart-item {
        padding: 15px;
    }
    
    .cart-item-image {
        width: 80px;
        height: 80px;
        margin-right: 15px;
    }
}

@media (max-width: 768px) {
    .cart-container {
        flex-direction: column;
        gap: 20px;
        padding: 15px;
    }
    
    .cart-main-content {
        flex: none;
        order: 1;
    }
    
    .cart-sidebar {
        flex: none;
        position: static;
        order: 2;
        width: 100%;
    }
    
    .cart-item {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .cart-item-image {
        width: 120px;
        height: 120px;
        margin: 0 auto 15px auto;
    }
    
    .cart-item-details {
        margin-bottom: 15px;
    }
    
    .cart-item-actions {
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .cart-item-actions form {
        display: flex;
        align-items: center;
        gap: 5px;
    }
}

@media (max-width: 480px) {
    .cart-container {
        padding: 10px;
    }
    
    .cart-item {
        padding: 15px;
    }
    
    .cart-item-image {
        width: 100px;
        height: 100px;
    }
    
    .quantity-input {
        width: 50px;
        padding: 6px;
    }
    
    .cart-btn {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
    
    .checkout-btn {
        padding: 12px 15px;
        font-size: 0.85rem;
    }
    
    .cart-summary {
        padding: 20px;
    }
}
</style>

<div class="cart-container">
    <div class="cart-main-content">
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Add some beautiful flowers to your cart to get started!</p>
                <a href="<?php echo APPURL; ?>1catalogue.php" class="continue-shopping">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <?php
                // Fetch fresh data from database to get description
                $stmt = $conn->prepare("SELECT description FROM shop WHERE fid = :id");
                $stmt->execute([':id' => $item['id']]);
                $dbItem = $stmt->fetch(PDO::FETCH_ASSOC);
                $description = $dbItem ? $dbItem['description'] : '';
                ?>
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
                    <?php if (!empty($description)): ?>
                        <div class="cart-item-description"><?php echo htmlspecialchars($description); ?></div>
                    <?php endif; ?>
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
                        <button type="submit" class="cart-btn remove-btn"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($_SESSION['cart'])): ?>
    <div class="cart-sidebar">
        <div class="cart-summary">
            <div class="cart-total">
                Total:<br>
                RS. <?php echo number_format($cart_total, 2); ?>
            </div>
            
            <a href="<?php echo APPURL; ?>1payment.php?cart=1" class="checkout-btn">
                Proceed to Checkout
            </a>
            
            <a href="<?php echo APPURL; ?>1catalogue.php" class="checkout-btn">
                Continue Shopping
            </a>
            
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="checkout-btn clear-cart">
                    Clear Cart
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require "includes/footer.php"; ?>
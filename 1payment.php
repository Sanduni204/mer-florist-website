<?php require "includes/header.php"; ?>
<link rel="stylesheet" type="text/css" href="<?php echo APPURL; ?>1payment.css">
<style>
/* Ensure consistent font family but preserve Font Awesome icons */
* {
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}

/* Preserve Font Awesome icons */
.fa, .fas, .far, .fal, .fab {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
}

/* Ensure search icon displays properly */
.nav-search i {
    font-family: "Font Awesome 6 Free" !important;
    font-weight: 900 !important;
}

/* Payment page specific styles */
.main-content {
    min-height: calc(100vh - 200px);
    padding: 20px 0;
}

.payment-container {
    display: flex;
    gap: 20px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Item details box on the left */
.item-details-box {
    flex: 0 0 300px;
    background: white;
    color: #2c3e50;
    padding: 25px;
    border-radius: 15px;
    border: 3px solid #fcd4e8;
    box-shadow: 0 10px 30px rgba(252, 212, 232, 0.3);
    height: fit-content;
    position: sticky;
    top: 90px;
    transition: all 0.3s ease;
}

.item-details-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(252, 212, 232, 0.4);
}

.item-details-box h3 {
    margin: 0 0 20px 0;
    font-size: 1.4rem;
    text-align: center;
    font-weight: 600;
}

.item-image {
    text-align: center;
    margin-bottom: 20px;
}

.item-image img {
    width: 100%;
    max-width: 180px;
    height: 150px;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    background: #f8f9fa;
}

.item-info {
    background: rgba(252, 212, 232, 0.1);
    padding: 15px;
    border-radius: 10px;
    border: 1px solid rgba(252, 212, 232, 0.3);
}

.item-info p {
    margin: 8px 0;
    font-size: 16px;
    line-height: 1.4;
}

.item-info .item-name {
    font-size: 1.2rem;
    font-weight: 600;
    text-align: center;
    margin-bottom: 15px;
}

.item-info .item-price {
    font-size: 1.3rem;
    font-weight: 700;
    text-align: center;
    background: rgba(252, 212, 232, 0.2);
    color: #ed7787;
    padding: 10px;
    border-radius: 8px;
    margin-top: 15px;
}

.item-detail-row {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
}

.item-detail-row .label {
    font-weight: 600;
    opacity: 0.9;
}

.item-detail-row .value {
    font-weight: 500;
}

/* Enhanced form styling to match item details box */
.payment-container .con {
    flex: 1;
    margin: 0;
    background: white;
    padding: 30px;
    border-radius: 15px;
    border: 3px solid #fcd4e8;
    box-shadow: 0 10px 30px rgba(252, 212, 232, 0.3);
    height: fit-content;
}

.payment_h2 {
    color: #2c3e50;
    font-size: 1.5rem;
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
    font-style: italic;
    position: relative;
}

.payment_h2::after {
    content: "";
    display: block;
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    margin: 10px auto 0;
    border-radius: 2px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #34495e;
    font-size: 14px;
    font-weight: 600;
    font-style: italic;
}

.form-group input[type="text"],
.form-group input[type="email"] {
    width: 100%;
    padding: 8px 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 13px;
    font-style: italic;
    transition: all 0.3s ease;
    background: #ffffff;
    box-sizing: border-box;
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}

.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus {
    outline: none;
    border-color: #ed7787;
    box-shadow: 0 0 15px rgba(237, 119, 135, 0.2);
    transform: translateY(-2px);
}

.form-group input[readonly] {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #6c757d;
    cursor: not-allowed;
}

/* Enhanced button styling */
button[type="submit"] {
    width: 100%;
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(237, 119, 135, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 15px;
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
}

button[type="submit"]:hover {
    background: linear-gradient(135deg, #fe98d7 0%, #ac789c 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(237, 119, 135, 0.4);
}

button[type="submit"]:active {
    transform: translateY(-1px);
}

/* Responsive payment layout */
@media (max-width: 1024px) {
    .item-details-box {
        flex: 0 0 280px;
        padding: 20px;
    }
    
    .payment-container .con {
        padding: 25px;
    }
}

@media (max-width: 768px) {
    .payment-container {
        flex-direction: column !important;
        gap: 15px !important;
    }
    .item-details-box {
        flex: none !important;
        position: static !important;
        order: -1;
    }
    .payment-container .con {
        flex: none !important;
        padding: 20px;
    }
    
    .payment_h2 {
        font-size: 1.3rem;
    }
    
    .form-group input[type="text"],
    .form-group input[type="email"] {
        padding: 8px 12px;
    }
}
</style>
<?php require "Config/config.php"; ?>
<?php
// Initialize variables
$flower = null;
$flowerName = '';
$flowerPrice = '';
$flowerImage = '';
$isCartCheckout = false;
$cartItems = [];
$cartTotal = 0;

// Check if this is a cart checkout
if (isset($_GET['cart']) && $_GET['cart'] == '1') {
    $isCartCheckout = true;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cartItems = $_SESSION['cart'];
        foreach ($cartItems as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }
    }
} elseif (isset($_GET['id'])) {
    // Get flower by ID from database
    try {
        $stmt = $conn->prepare("SELECT * FROM shop WHERE fid = :id");
        $stmt->execute([':id' => (int)$_GET['id']]);
        $flower = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($flower) {
            $flowerName = $flower['name'];
            $flowerPrice = number_format((float)$flower['price'], 2);
            $flowerImage = $flower['image'];
        }
    } catch (Throwable $e) {
        // Ignore database errors for production
    }
} elseif (isset($_GET['name']) && isset($_GET['price'])) {
    // Get flower info from URL parameters
    $flowerName = htmlspecialchars($_GET['name']);
    $flowerPrice = htmlspecialchars($_GET['price']);
    $flowerImage = htmlspecialchars($_GET['image'] ?? '');
}
?>

<div class="main-content">
<div class="payment-container">
    <!-- Item Details Box on the Left -->
    <?php if ($isCartCheckout && !empty($cartItems)): ?>
    <div class="item-details-box">
        <h3>Cart Items</h3>
        <?php foreach ($cartItems as $item): ?>
            <div style="margin-bottom: 15px; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <?php if ($item['image']): ?>
                        <img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                    <?php endif; ?>
                    <div>
                        <p style="margin: 0; font-weight: 600; font-size: 14px;"><?php echo htmlspecialchars($item['name']); ?></p>
                        <p style="margin: 0; font-size: 12px; opacity: 0.9;">Qty: <?php echo $item['quantity']; ?> × RS. <?php echo number_format($item['price'], 2); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div style="border-top: 2px solid rgba(255,255,255,0.3); padding-top: 15px; margin-top: 15px;">
            <div style="display: flex; justify-content: space-between; font-size: 1.1rem; font-weight: 600;">
                <span>Total:</span>
                <span>RS. <?php echo number_format($cartTotal, 2); ?></span>
            </div>
        </div>
    </div>
    <?php elseif ($flower): ?>
    <div class="item-details-box">
        <h3>Selected Item</h3>
        <div class="item-image">
            <?php if ($flowerImage): ?>
                <img src="<?php echo APPURL; ?>Images/<?php echo $flowerImage; ?>" alt="<?php echo $flowerName; ?>" />
            <?php else: ?>
                <div style="width:100%;height:200px;background:#f0f0f0;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#999;">
                    No Image Available
                </div>
            <?php endif; ?>
        </div>
        <div class="item-info">
            <p class="item-name"><?php echo htmlspecialchars($flowerName); ?></p>
            
            <?php if (!empty($flower['type'])): ?>
            <div class="item-detail-row">
                <span class="label">Type:</span>
                <span class="value"><?php echo htmlspecialchars($flower['type']); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($flower['color_theme'])): ?>
            <div class="item-detail-row">
                <span class="label">Color:</span>
                <span class="value"><?php echo htmlspecialchars($flower['color_theme']); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($flower['description'])): ?>
            <div class="item-detail-row">
                <span class="label">Description:</span>
            </div>
            <p style="margin-top:5px;font-size:14px;opacity:0.9;line-height:1.3;">
                <?php echo htmlspecialchars($flower['description']); ?>
            </p>
            <?php endif; ?>
            
            <p class="item-price">Rs. <?php echo $flowerPrice; ?></p>
        </div>
    </div>
    <?php else: ?>
    <div class="item-details-box">
        <h3>No Item Selected</h3>
        <div style="text-align:center;padding:20px;">
            <i class="fas fa-flower" style="font-size:3rem;opacity:0.5;margin-bottom:15px;"></i>
            <p style="opacity:0.8;font-size:16px;line-height:1.4;">
                Please select a flower bouquet from our catalogue to proceed with payment.
            </p>
            <a href="<?php echo APPURL; ?>1catalogue.php" style="display:inline-block;margin-top:15px;padding:10px 20px;background:rgba(255,255,255,0.2);color:white;text-decoration:none;border-radius:25px;transition:all 0.3s ease;">
                Browse Catalogue
            </a>
        </div>
    </div>
    <?php endif; ?>
    <div class="con" style="width: 100%;">
        <form method="POST">
            <div style="display: flex; gap: 30px;">
                <!-- Left Column: Customer Details -->
                <div style="flex: 1;">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="SH perera" required >
                    </div>
                    <div class="form-group">
                        <label for="ContactN">Contact Number</label>
                        <input type="text" id="ContactN" name="ContactN"  placeholder="0778654234"  required>
                    </div>
                    <div class="form-group">
                        <label for="Address">Address</label>
                        <input type="text" id="Address" name="Address" placeholder="112, Colombo 7" required>
                    </div>
                    <div class="form-group">
                        <label for="pcode">Postal code</label>
                        <input type="text" id="pcode" name="pcode" placeholder="1124" required>
                    </div>
                    <div class="form-group">
                        <label for="Iname"><?php echo $isCartCheckout ? "Order Description" : "Item's Name"; ?></label>
                        <input type="text" id="Iname" name="Iname" 
                               value="<?php echo $isCartCheckout ? 'Cart Checkout - Multiple Items' : htmlspecialchars($flowerName); ?>" 
                               placeholder="<?php echo $isCartCheckout ? 'Cart Checkout' : 'Daisy Dazzle'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (Rs.)</label>
                        <input type="text" id="price" name="price" 
                               value="<?php echo $isCartCheckout ? number_format($cartTotal, 2) : htmlspecialchars($flowerPrice); ?>" 
                               placeholder="0.00" readonly>
                    </div>
                </div>
                
                <!-- Right Column: Payment Details -->
                <div style="flex: 1;">
                    <div class="form-group">
                        <label for="cardname">Name on Card</label>
                        <input type="text" id="cardname" name="cardname" placeholder="John Doe" required >
                    </div>
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card-number"  placeholder="1100-2345-4567-2345"  required>
                    </div>
                    <div class="form-group">
                        <label for="exp-date">Expiration Date</label>
                        <input type="text" id="exp-date" name="exp-date" placeholder="MM/YY" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" required>
                    </div>
                    <button type="submit" id="paymentButton">Complete Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<?php require "includes/footer.php"; ?>
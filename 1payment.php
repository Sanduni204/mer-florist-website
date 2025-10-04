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

.selected-flower {
    background: #f9f9f9;
    padding: 20px;
    margin: 20px auto;
    border-radius: 8px;
    text-align: center;
    max-width: 1200px;
}

.payment-container {
    display: flex;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.payment-container .con {
    flex: 1;
    margin: 0;
}

/* Responsive payment layout */
@media (max-width: 768px) {
    .payment-container {
        flex-direction: column !important;
        gap: 10px !important;
    }
    .payment-container .con {
        flex: none !important;
    }
}
</style>
<?php require "Config/config.php"; ?>
<?php
// Get flower information from URL parameters or database
$flower = null;
$flowerName = '';
$flowerPrice = '';
$flowerImage = '';

if (isset($_GET['id'])) {
    // Get flower by ID from database
    try {
        $stmt = $conn->prepare("SELECT * FROM shop WHERE id = :id");
        $stmt->execute([':id' => (int)$_GET['id']]);
        $flower = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($flower) {
            $flowerName = $flower['name'];
            $flowerPrice = number_format((float)$flower['price'], 2);
            $flowerImage = $flower['image'];
        }
    } catch (Throwable $e) {
        // Ignore database errors
    }
} elseif (isset($_GET['name']) && isset($_GET['price'])) {
    // Get flower info from URL parameters
    $flowerName = htmlspecialchars($_GET['name']);
    $flowerPrice = htmlspecialchars($_GET['price']);
    $flowerImage = htmlspecialchars($_GET['image'] ?? '');
}
?>

<div class="main-content">
<?php if ($flowerName): ?>
    <div class="selected-flower">
        <h3>Selected Flower</h3>
        <?php if ($flowerImage): ?>
            <img src="<?php echo APPURL; ?>Images/<?php echo $flowerImage; ?>" alt="<?php echo $flowerName; ?>" style="width:150px;height:150px;object-fit:cover;border-radius:8px;margin:10px;" />
        <?php endif; ?>
        <p><strong><?php echo $flowerName; ?></strong></p>
        <p>Price: Rs. <?php echo $flowerPrice; ?></p>
    </div>
<?php endif; ?>

<div class="payment-container" style="display: flex; gap: 20px; max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="con" style="flex: 1;">
        <h2 class="payment_h2">Enter your details</h2>
        <form method="POST">
            <div class="form-group">
                <label for="Name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="SH perera" required >
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="text" id="ContactN" name="ContactN"  placeholder="0778654234"  required>
            </div>
            <div class="form-group">
                <label for="Address">Address</label>
                <input type="text" id="Address" name="Address" placeholder="112, Colombo 7" required>
            </div>
            <div class="form-group">
                <label for="PostalCode">Postal code</label>
                <input type="text" id="pcode" name="pcode" placeholder="1124" required>
            </div>
            <div class="form-group">
                <label for="Item's name">Item's Name</label>
                <input type="text" id="Iname" name="Iname" value="<?php echo htmlspecialchars($flowerName); ?>" placeholder="Daisy Dazzle" required>
            </div>
            <div class="form-group">
                <label for="price">Price (Rs.)</label>
                <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($flowerPrice); ?>" placeholder="0.00" readonly style="background:#f5f5f5;">
            </div>
            
            
        </form>
    </div>
    <div class="con" style="flex: 1;">
        <h2 class="payment_h2">Make your payment </h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name on Card</label>
                <input type="text" id="name" name="name" placeholder="Name" required >
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
                <input type="text" id="cvv" name="cvv" placeholder="112" required>
            </div>
            <button type="submit" id="paymentButton">Pay</button>
            
        </form>
    </div>
</div>
</div>

<?php require "includes/footer.php"; ?>
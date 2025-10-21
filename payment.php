<?php require "includes/header.php"; ?>
<link rel="stylesheet" type="text/css" href="<?php echo APPURL; ?>payment.css">
<?php
require "Config/config.php";
$itemName = 'DAILY ESSENTIAL BUNDLE';
$itemQty = 1;
$itemPrice = 2360.00;
$itemImage = 'sample-bundle.jpg';
$shipping = 400.00;
$total = $itemPrice * $itemQty + $shipping;

if (isset($_GET['id'])) {
  $fid = intval($_GET['id']);
  $stmt = $conn->prepare("SELECT name, price, image FROM shop WHERE fid = :fid LIMIT 1");
  $stmt->execute([':fid' => $fid]);
  $item = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($item) {
    $itemName = htmlspecialchars($item['name']);
    $itemPrice = floatval($item['price']);
    $itemImage = htmlspecialchars($item['image']);
    $total = $itemPrice * $itemQty + $shipping;
  }
}
?>
<div class="main-content">
  <div class="checkout-container">
    <div class="checkout-left">
      <h2 class="section-title">Contact</h2>
      <input type="text" class="input" placeholder="Email or mobile phone number">
      <label class="checkbox-label"><input type="checkbox"> Email me with news and offers</label>
      <h2 class="section-title">Delivery</h2>
      <select class="input"><option>Sri Lanka</option></select>
      <div class="row">
        <input type="text" class="input" placeholder="First name (optional)">
        <input type="text" class="input" placeholder="Last name">
      </div>
      <input type="text" class="input" placeholder="Company (optional)">
      <input type="text" class="input" placeholder="Address">
      <input type="text" class="input" placeholder="Apartment, suite, etc. (optional)">
      <div class="row">
        <input type="text" class="input" placeholder="City">
        <input type="text" class="input" placeholder="Postal code (optional)">
      </div>
      <input type="text" class="input" placeholder="Phone">
      <label class="checkbox-label"><input type="checkbox"> Save this information for next time</label>
      <h2 class="section-title">Shipping method</h2>
      <div class="shipping-box selected">
        <div>Standard</div>
        <div class="shipping-desc">Delivery Within 2-5 Working Days</div>
        <div class="shipping-price">Rs <?php echo number_format($shipping,2); ?></div>
      </div>
      <h2 class="section-title">Payment</h2>
      <div class="payment-box selected">
        <div class="payment-method">
          <input type="radio" name="paymethod" checked> Onepay
          <span class="card-icons">
            <img src="Images/visa.png" alt="Visa" />
            <img src="Images/mastercard.png" alt="Mastercard" />
            <img src="Images/discover.png" alt="Discover" />
            <span class="more">+3</span>
          </span>
        </div>
        <div class="payment-desc">After clicking "Pay now", you will be redirected to Onepay to complete your purchase securely.</div>
      </div>
      <div class="payment-box">
        <div class="payment-method">
          <input type="radio" name="paymethod"> Cash on Delivery (COD)
        </div>
      </div>
      <h2 class="section-title">Billing address</h2>
      <div class="billing-box selected">
        <input type="radio" name="billing" checked> Same as shipping address
      </div>
      <div class="billing-box">
        <input type="radio" name="billing"> Use a different billing address
      </div>
      <form action="payment.php" method="get">
        <button type="submit" class="paynow-btn">Pay now</button>
      </form>
    </div>
    <div class="checkout-right">
      <div class="cart-item">
        <img src="Images/<?php echo $itemImage; ?>" class="cart-img" alt="<?php echo $itemName; ?>">
        <div class="cart-info">
          <div class="cart-title"><?php echo $itemName; ?></div>
          <div class="cart-qty"><?php echo $itemQty; ?></div>
          <div class="cart-price">Rs <?php echo number_format($itemPrice,2); ?></div>
        </div>
      </div>
      <div class="cart-summary">
        <div class="summary-row"><span>Subtotal</span><span>Rs <?php echo number_format($itemPrice * $itemQty,2); ?></span></div>
        <div class="summary-row"><span>Shipping</span><span>Rs <?php echo number_format($shipping,2); ?></span></div>
        <div class="summary-row total"><span>Total</span><span class="total-price">LKR Rs <?php echo number_format($total,2); ?></span></div>
      </div>
    </div>
  </div>
</div>
<?php require "includes/footer.php"; ?>
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

  $merchant_id = "1232562";
  $order_id = isset($fid) ? $fid : 'ItemNo12345';
  $amount = floatval($total); // Ensure $amount is float
  $currency = "LKR";
  $merchant_secret = "OTc2MDU5NzEzMTI2MjIyNDg0MjM5NjQ2NjU5MTkzOTUxODY3MTQy";

  $hash = strtoupper(
    md5(
      $merchant_id .
      $order_id .
      number_format($amount, 2, '.', '') .
      $currency .
      strtoupper(md5($merchant_secret))
    )
  );
  }
}
?>
<div class="main-content">
  <div class="checkout-container">
    <div class="checkout-left">
      <h2 class="section-title">Contact</h2>
      <input type="text" class="input" placeholder="Email">
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
      <!-- Payment heading and Onepay/UI labels removed as requested -->
      <!-- PayHere Sandbox Integration Button remains -->
      <!-- PayHere Sandbox Integration Button -->
  <button id="payhere-payment" class="paynow-btn" type="button">Pay now</button>
      </div>
      <!-- Cash on Delivery option removed -->
      <!-- Billing address section removed as requested -->
    </div>
    <div class="checkout-right">
      <?php
      // If cart flag is present, render all items from session cart
      $displayCart = [];
      if (isset($_GET['cart']) && $_GET['cart'] == '1') {
          // prefer session cart, but if logged in and session empty, pull from DB
          if (!empty($_SESSION['cart'])) {
              $displayCart = $_SESSION['cart'];
          } elseif (isset($_SESSION['user_id'])) {
              $uid = $_SESSION['user_id'];
              $stmt = $conn->prepare("SELECT c.item_id AS id, s.name, s.price, s.image, c.quantity FROM cart_items c JOIN shop s ON c.item_id = s.fid WHERE c.user_id = :uid");
              $stmt->execute([':uid' => $uid]);
              $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($rows as $r) {
                  $displayCart[] = [
                      'id' => $r['id'],
                      'name' => $r['name'],
                      'price' => $r['price'],
                      'image' => $r['image'],
                      'quantity' => $r['quantity']
                  ];
              }
          }
      } else {
          // fall back to single item view (existing behavior)
          $displayCart = [[ 'id' => $fid ?? 0, 'name' => $itemName, 'price' => $itemPrice, 'image' => $itemImage, 'quantity' => $itemQty ]];
      }

      $subtotal = 0;
      foreach ($displayCart as $d) {
          $qty = isset($d['quantity']) ? intval($d['quantity']) : 1;
          $price = isset($d['price']) ? floatval($d['price']) : 0.0;
          $subtotal += $price * $qty;
      ?>
      <div class="cart-item">
        <img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($d['image'] ?? 'sample-bundle.jpg'); ?>" class="cart-img" alt="<?php echo htmlspecialchars($d['name']); ?>">
        <div class="cart-info">
          <div class="cart-title"><?php echo htmlspecialchars($d['name']); ?></div>
          <div class="cart-qty"><?php echo $qty; ?></div>
          <div class="cart-price">Rs <?php echo number_format($price,2); ?></div>
        </div>
      </div>
      <?php } // end loop ?>

      <div class="cart-summary">
        <div class="summary-row"><span>Subtotal</span><span>Rs <?php echo number_format($subtotal,2); ?></span></div>
        <div class="summary-row"><span>Shipping</span><span>Rs <?php echo number_format($shipping,2); ?></span></div>
        <div class="summary-row total"><span>Total</span><span class="total-price">LKR Rs <?php echo number_format($subtotal + $shipping,2); ?></span></div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
<script type="text/javascript">
  // Payment completed. It can be a successful failure.
  payhere.onCompleted = function onCompleted(orderId) {
    console.log("Payment completed. OrderID:" + orderId);
    // Note: validate the payment and show success or failure page to the customer
  };

  // Payment window closed
  payhere.onDismissed = function onDismissed() {
    // Note: Prompt user to pay again or show an error page
    console.log("Payment dismissed");
  };

  // Error occurred
  payhere.onError = function onError(error) {
    // Note: show an error page
    console.log("Error:"  + error);
  };

  // Collect form values and update payment object before starting PayHere
  document.getElementById('payhere-payment').onclick = function (e) {
    var payment = {
      "sandbox": true,
      "merchant_id": "1232562",    // Replace your Merchant ID
      "return_url": undefined,     // Important
      "cancel_url": undefined,     // Important
      "notify_url": "http://sample.com/notify",
      "order_id": "<?php echo isset($fid) ? $fid : 'ItemNo12345'; ?>",
      "items": "<?php echo addslashes($itemName); ?>",
      "amount": "<?php echo number_format($total,2,'.',''); ?>",
      "currency": "LKR",
      "hash": "<?php echo isset($hash) ? $hash : ''; ?>", // *Replace with generated hash retrieved from backend
      "first_name": document.querySelector('input[placeholder="First name (optional)"]').value || 'Guest',
      "last_name": document.querySelector('input[placeholder="Last name"]').value || '',
      "email": document.querySelector('input[placeholder="Email"]').value || '',
      "phone": document.querySelector('input[placeholder="Phone"]').value || '',
      "address": document.querySelector('input[placeholder="Address"]').value || '',
      "city": document.querySelector('input[placeholder="City"]').value || '',
      "country": "Sri Lanka",
      "delivery_address": document.querySelector('input[placeholder="Address"]').value || '',
      "delivery_city": document.querySelector('input[placeholder="City"]').value || '',
      "delivery_country": "Sri Lanka",
      "custom_1": "",
      "custom_2": ""
    };
    payhere.startPayment(payment);
  };
</script>
<?php require "includes/footer.php"; ?>
<?php 
// Set page info for header
$GLOBALS['current_page'] = 'search';
$GLOBALS['search_filters'] = [
    'type' => $_POST['types'] ?? ($_GET['types'] ?? null),
    'color_theme' => $_POST['color_theme'] ?? ($_GET['color_theme'] ?? null)
];
require "includes/header.php"; 
?>
<main class="main-content">
<?php
require_once "Config/config.php";

// Accept filters from POST (initial search) or GET (when sorting)
$type = $_POST['types'] ?? ($_GET['types'] ?? null);
$color_theme = $_POST['color_theme'] ?? ($_GET['color_theme'] ?? null);
// Sort order via GET
$sortOrder = isset($_GET['price']) ? strtoupper($_GET['price']) : null;

if ($type === null || $color_theme === null) {
    header("location: ".APPURL."1home.php");
    exit;
}

// Build filtered query with optional sort
$sql = "SELECT * FROM shop WHERE type LIKE :type AND color_theme LIKE :color_theme";
if ($sortOrder === 'ASC' || $sortOrder === 'DESC') {
    $sql .= " ORDER BY price " . $sortOrder;
}

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':type' => "%{$type}%",
    ':color_theme' => "%{$color_theme}%",
]);
$listings = $stmt->fetchAll(PDO::FETCH_OBJ);

?>
<?php
$cartIds = [];
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cartItem) {
        if (isset($cartItem['id'])) {
            $cartIds[] = $cartItem['id'];
        }
    }
}
?>
<div id="fitems" class="search-results">
   
   <div class="front2">
    
    <?php if(!empty($listings) && count($listings)>0) :?>
        <?php foreach($listings as $listing) : ?>
            <?php $listingId = isset($listing->fid) ? (int)$listing->fid : 0; ?>
            <div class="f">
            <img class="img" src=".\Images\<?php echo $listing->image ; ?>">
            <p><?php echo $listing->name; ?></p>
            <p>RS.<?php echo $listing->price; ?>.00</p>
            <p><B><?php echo $listing->description; ?></B></p>
            <div class="item-buttons">
                <button onclick="addToCart(<?php echo $listingId; ?>)" class="add-to-cart-btn<?php echo in_array($listingId, $cartIds) ? ' clicked' : ''; ?>" data-id="<?php echo $listingId; ?>" <?php echo in_array($listingId, $cartIds) ? 'disabled' : ''; ?>><?php echo in_array($listingId, $cartIds) ? 'Added!' : 'Add to Cart'; ?></button>
                <a href="1payment.php?id=<?php echo $listingId; ?>" class="pay-now-btn">Pay Now</a>
            </div>
            </div>
        
<?php endforeach; ?>
<?php else :?>
    <div class="bg-success text-white">we do not have any listing with this query for
    now </div><br><br>
    <?php endif; ?>
    

    

    </div> <!-- /.front2 -->
</div> <!-- /#fitems -->
</main>

<?php require "includes/footer.php"; ?>
</main>

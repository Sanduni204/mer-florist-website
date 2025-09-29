<?php require "includes/header.php"; ?>
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
<div class="sort-dropdown">
        <button class="dropdown-btn" onclick="toggleDropdown()">
            <span id="selectedOption">Sort by</span>
            <span class="dropdown-arrow" id="dropdownArrow">▼</span>
        </button>
        <div class="dropdown-content" id="dropdownContent">
            <?php $qType = urlencode($type); $qColor = urlencode($color_theme); ?>
            <a href="search.php?types=<?php echo $qType; ?>&color_theme=<?php echo $qColor; ?>&price=ASC" class="dropdown-item" id="dropdown-item-1">Price Ascending</a>
            <a href="search.php?types=<?php echo $qType; ?>&color_theme=<?php echo $qColor; ?>&price=DESC" class="dropdown-item" id="dropdown-item-2">Price Descending</a>
        </div>
    </div><br><br>

<div id="fitems"><br><br><br>
   
   <div class="front2">
    
    <?php if(!empty($listings) && count($listings)>0) :?>
        <?php foreach($listings as $listing) : ?>
            <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\<?php echo $listing->image ; ?>">
            <p><?php echo $listing->name; ?></p>
            <p>RS.<?php echo $listing->price; ?>.00</p>
            <p><B><?php echo $listing->description; ?></B></p>
            </div></a>
        
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

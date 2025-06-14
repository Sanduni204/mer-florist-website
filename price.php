<?php require "includes/header.php"; ?>
<?php
require_once "Config/config.php";


if(isset($_GET['price'])){
    $price = $_GET['price'];

    // Sanitize the input to prevent SQL injection
    if ($price == 'ASC' || $price == 'DESC') {
        $price_query = $conn->prepare("SELECT * FROM shop ORDER BY price $price");
        $price_query->execute();
        $allListingsPrice = $price_query->fetchAll(PDO::FETCH_OBJ);
    } else {
        // Handle invalid input (e.g., display an error message)
        echo "Invalid sorting option.";
        exit;
    }
}



?>
<div class="sort-dropdown">
        <button class="dropdown-btn" onclick="toggleDropdown()">
            <span id="selectedOption">Sort by</span>
            <span class="dropdown-arrow" id="dropdownArrow">▼</span>
        </button>
        <div class="dropdown-content" id="dropdownContent">
            
        
<a href="price.php?price=ASC" class="dropdown-item" onclick="selectOption('Price Ascending')">Price Ascending</a>
            <a href="price.php?price=DESC" class="dropdown-item" onclick="selectOption('Price Descending')">Price Descending</a>
        </div>
    </div><br><br>


<div id="fitems"><br><br><br>
   
   <div class="front2">
    
   
        <?php foreach($allListingsPrice as $listing) : ?>
            <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\<?php echo $listing->image ; ?>">
            <p><?php echo $listing->name; ?></p>
            <p>RS.<?php echo $listing->price; ?>.00</p>
            <p><B><?php echo $listing->description; ?></B></p>
            </div></a>
        
<?php endforeach; ?>

    

    

   <?php require "includes/footer.php"; ?>
</div>

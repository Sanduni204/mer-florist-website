<?php require "includes/header.php"; ?>
<?php
require_once "Config/config.php";

$select=$conn->query("SELECT*FROM shop");
$select->execute();

$shop = $select->fetchAll(PDO::FETCH_OBJ);

if(isset($_POST['submit'])){
    $type = $_POST['types'];
    $color_theme = $_POST['color_theme'];

    $search = $conn->query("SELECT*FROM shop WHERE type LIKE '%$type%' AND 
    color_theme LIKE '%$color_theme%'");
    // % for wild card 
    $search->execute();

    $listings = $search->fetchALL(PDO::FETCH_OBJ);

}else{
    header("location: ".APPURL."1home.php");
}

?>
<?php if(isset($_POST['submit'])): ?>
<div id="fitems"><br><br><br>
   
   <div class="front2">
    
   <?php if(count($listings)>0) :?>
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
    

    

   <?php require "includes/footer.php"; ?>
</div>
<?php endif; ?>

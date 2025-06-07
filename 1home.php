<?php require "includes/header.php"; ?>
<?php
require_once "Config/config.php";

$select=$conn->query("SELECT*FROM shop WHERE description='Best seller'OR description = 'delivery free' LIMIT 5");
$select->execute();

$shop = $select->fetchAll(PDO::FETCH_OBJ);

   

?>


<div class="container">
  <img src=".\Images\front_top.jpg" style="width:100%;">
  <div class="content">
    <p class="top">Discover <i>Mer</i>:<br>Where Every Petal Tells a Story.</p><br><br>
    <p class="bottom">Shop the Finest Blooms for Every Occassion!</p>
  </div>
</div>

  <div class="search-section">
    <div class="search-container">
        <h2 class="search-title">Find Your Perfect Bouquet</h2>
        <p class="search-subtitle">Discover beautiful flowers for every occasion</p>
        
<form class="search-form" action="search.php" method="POST">
            <div class="search-input-group">
                <select name="types" class="category-select">
                <option value="">Pick a bouquet</option>
                <option value="Rose Bouquets">Rose Bouquets</option>
                <option value="Lily Bouquets">Lily Bouquets</option>
                <option value="Daisy Bouquets">Daisy Bouquets</option>
                <option value="Tulip Bouquets">Tulip Bouquets</option>
                <option value="Sunflower Bouquets">Sunflower Bouquets</option>
                <option value="Hydrangea Bouquets">Hydrangea Bouquets</option>
            </select>
            </div>

            <div class="search-input-group">
                <select name="color_theme" class="category-select">
                <option value="">Pick a color</option>                
                <option value="Red">Red</option>
                <option value="White">White</option>
                <option value="Yellow">Yellow</option>
                <option value="Blue">Blue</option>
                <option value="Orange">Orange</option>
                <option value="Pink">Pink</option>
                <option value="Purple">Purple</option>
                <option value="Mix">Mix</option>
                </select>
            </div>
                       
            <button type="submit" class="search-btn" name="submit">
                <i class="fas fa-search"></i>
                Search
            </button>
        </form>
        
        
    </div>
</div>


<div id="fitems"><br><br><br>
   <h3 class="sub">Featured Items</h3>
   <div class="front2">
    
   
        <?php foreach($shop as $sho) : ?>
            <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\<?php echo $sho->image ; ?>">
            <p><?php echo $sho->name; ?></p>
            <p>RS.<?php echo $sho->price; ?>.00</p>
            <p><B><?php echo $sho->description; ?></B></p>
            </div></a>
        
<?php endforeach; ?>
    </div>



<div id="aboutsec"><br><br><br>

   <div id="aboutsec"><br><br><br>
    <h3 class ="sub">About us</h3>
 <div class="front3">
     <p class="work">
     <img src=".\Images\work.jpg" >
     Founded in 2000, <i>mer</i> started as a small family-owned flower shop with 
          a big dream: to spread joy and happiness through the beauty of flowers. Over the years, we've grown
           into a thriving business, serving customers across Sri Lanka with our commitment to quality,
           creativity, and exceptional customer service.<br><br>
      At <i>mer</i> we're passionate about providing the freshest and most beautiful 
         flowers for every occasion.Whether you're celebrating a special milestone, 
         expressing love and appreciation, or simply brightening someone's day,
          we're here to help you find the perfect floral arrangements to convey your heartfelt sentiments.</p>
          
 </div>

<h4 class="front_sub">Why choose us?</h4>
<div class="front3">
        <div class="abt" id="abt1">
            <img src =".\Images\choose1.jpeg">
            <p class="big">Quality Assurance</p>
            <p class="small">We hand-select only the 
                freshest and highest-quality flowers for our arrangements, 
                guaranteeing longevity and beauty.</p>
        </div>

        <div class="abt" id="abt2">
            <img class="img" src=".\Images\choose2.jpeg">
            <p class="big">Personalized Service</p>
            <p class="small">From custom orders to special requests, 
                our dedicated team is here to make your floral vision a 
                reality.</p>
        </div>

        <div class="abt" id="abt3">
            <img class="img" src=".\Images\choose3.png">
            <p class="big">Convenience</p>
            <p class="small">With easy online ordering and prompt
                 delivery, sending flowers has never been easier or 
                 more convenient.</p>
        </div>

        <div class="abt" id="abt4">
            <img class="img" src=".\Images\choose4.png">
            <p class="big">Customer Satisfaction</p>
            <p class="small">Your satisfaction is our top priority. 
                We go above and beyond to ensure that every customer
                 has a positive experience with us.</p>
        </div>
    </div>
    </div>
   </div>

   
   



   <?php require "includes/footer.php"; ?>

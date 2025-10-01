<?php require "includes/header.php"; ?>
<?php require "Config/config.php";

// Determine a safe ORDER BY column: prefer primary key; fallback to created_at, id/ID, or name
$orderCol = 'name';
$hasFeaturedCol = false; // will not be used further; kept to avoid extra logic
try {
    $cols = $conn->query('SHOW COLUMNS FROM shop')->fetchAll(PDO::FETCH_ASSOC);
    if ($cols) {
        $fields = array_column($cols, 'Field');
        foreach ($cols as $c) { if (!empty($c['Key']) && strtoupper($c['Key']) === 'PRI') { $orderCol = $c['Field']; break; } }
        if ($orderCol === 'name') {
            if (in_array('created_at', $fields, true)) { $orderCol = 'created_at'; }
            elseif (in_array('id', $fields, true)) { $orderCol = 'id'; }
            elseif (in_array('ID', $fields, true)) { $orderCol = 'ID'; }
        }
        $hasFeaturedCol = in_array('featured', $fields, true);
    }
} catch (Throwable $e) { /* keep defaults */ }

// Featured Items: Show ALL items that have a non-empty description
try {
    $sql = "SELECT * FROM shop WHERE description IS NOT NULL AND TRIM(description) <> '' ORDER BY $orderCol DESC";
    $stmt = $conn->query($sql);
    $stmt->execute();
    $shop = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (Throwable $e) {
    // Fallback to any items
    try {
        $select = $conn->query("SELECT * FROM shop ORDER BY $orderCol DESC");
        $select->execute();
        $shop = $select->fetchAll(PDO::FETCH_OBJ);
    } catch (Throwable $e2) {
        $shop = [];
    }
}

?>


<div class="container home-hero">
  <img src=".\Images\front_top.jpg" style="width:100%;">
  <div class="content">
    <p class="top">Discover <i>Mer</i>:<br>Where Every Petal Tells a Story.</p><br><br>
    <p class="bottom">Shop the Finest Blooms for Every Occassion!</p>
  </div>
</div>

  


<section id="fitems">
    <h3 class="sub">Featured Items</h3>
    <div class="front2">
    
   
                <?php foreach($shop as $sho) : ?>
                        <a href="1payment.html"><div class="f">
                        <img class="img" src=".\Images\<?php echo $sho->image ; ?>">
                        <p><?php echo $sho->name; ?></p>
                        <p>RS.<?php echo number_format((float)$sho->price, 2); ?></p>
                                    <?php if (!empty($sho->description)) : ?>
                                        <p><b><?php echo htmlspecialchars($sho->description); ?></b></p>
                                    <?php endif; ?>
                        </div></a>
                <?php endforeach; ?>
    </div>
</section>


<section id="aboutsec">
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
</section>

<!-- Why choose us moved to the end of the page -->
<section id="whychoose">
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
</section>

   
   



    <?php require "includes/footer.php"; ?>

<?php 

require "includes/header.php"; ?>


<header>
    
    <div class="menubar">
        <nav>
            <ul class="main">
                <li><a href="<?php echo APPURL; ?>/1home.php" id="home-link" class="navlink">Home</a></li>
                <li><a href="<?php echo APPURL; ?>/1catalogue.php" id="catalogue-link" class="navlink">Shop</a></li>
                <li><a href="<?php echo APPURL; ?>/1contact.php" id="contact-link" class="navlink">Contact</a></li>
                <li><a href="<?php echo APPURL; ?>/auth/1register.php" id="register-link" class="navlink">Sign-up</a></li>
                <li><a href="<?php echo APPURL; ?>/auth/1login.php" id="login-link" class="navlink">Sign-in</a></li>
                
<?php if(isset($_SESSION['username'])) : ?>
                <li class="drop">
                <a href="#"><?php //echo $_SESSION['username']; ?></a>
                <ul class="dropdown arrow-top">
                <li><a href="<?php echo APPURL; ?>/auth/logout.php">Logout</a></li>
                
            </ul>
                </li>
<?php else : ?>
    <li><a href="<?php echo APPURL; ?>/auth/1register.php" id="register-link" class="navlink">Sign-up</a></li>
    <li><a href="<?php echo APPURL; ?>/auth/1login.php" id="login-link" class="navlink">Sign-in</a></li>
    <?php endif; ?>
                         
                    
                      
                      
                    
                <img src="<?php echo APPURL; ?>/Images/logo.png" class="logo">

            </ul>
        </nav>
    </div>
</header>

<div class="container">
  <img src=".\Images\front_top.jpg" style="width:100%;">
  <div class="content">
    <p class="top">Discover <i>Mer</i>:<br>Where Every Petal Tells a Story.</p><br><br>
    <p class="bottom">Shop the Finest Blooms for Every Occassion!</p>
  </div>
</div>

  

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

   <div id="fitems"><br><br><br>
   <h3 class="sub">Featured items</h3>
   <div class="front2">
        <a href="1payment.html"><div class="f">
        <img class="img" src=".\Images\Rose6.jpg">
            <p>Secret love</p>
            <p>RS.4000.00</p>
            <p><B>Best Seller*</B></p>
        </div></a>

        <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\Lily6.jpg">
            <p>Perfection Lily</p>
            <p>RS.3000.00</p>
            <p><B>Delivery Free*</B></p>
        </div></a>

        <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\Daisy2.jpg">
            <p>Glorious Daisy</p>
            <p><s>RS.3000.00</s></p>
            <p>RS.2700.00</p>
        </div></a>

        <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\Tulip5.jpg">
            <p>Tulip Love</p>
            <p>RS.5000.00</p>
            <p><B>Best Seller*</B></p>
        </div></a>

        <a href="1payment.html"><div class="f">
            <img class="img" src=".\Images\Sunflower4.jpg">
            <p>Mini Sunny Bunch</p>
            <p>RS.2100.00</p>
            <p><B>New Ariival*</B></p>
        </div></a>
   </div>

   <?php require "includes/footer.php"; ?>

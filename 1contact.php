<?php require "includes/header.php"; ?>

<header>
    
    <div class="menubar">
        <nav>
            <ul class="main">
                <li><a href="<?php echo APPURL; ?>/1home.php" id="home-link" class="navlink">Home</a></li>
                <li><a href="<?php echo APPURL; ?>/1catalogue.php" id="catalogue-link" class="navlink">Shop</a></li>
                <li><a href="<?php echo APPURL; ?>/1contact.php" id="contact-link" class="navlink">Contact</a></li>  
                <li><a href="<?php echo APPURL; ?>/auth/1register.php" id="register-link" class="navlink">Sign-up</a></li>
                <li><a href="<?php echo APPURL; ?>/auth/1login.php" id="login-link" class="navlink">Sign-in</a></li>
                <img src="<?php echo APPURL; ?>/Images/logo.png" class="logo">

            </ul>
        </nav>
    </div>
</header>
<h3 class="meet_us">Let's Talk</h3>
        <div id="info">
            <div class="col1">
                <img src=".\Images\shop.png">
            </div>
            <div class="col2">
               
                <p><i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;Barnes Pl, Colombo 07</p>
                <p><i class="fa-solid fa-envelope"></i>&nbsp;&nbsp;&nbsp;mer_shopping@gmail.com</p>
                <p><i class="fa-solid fa-phone"></i>&nbsp;&nbsp;&nbsp;0112345678</p><br>
                <p><a href="#"><i class="fa-brands fa-instagram"></i></a>&nbsp;&nbsp;
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>&nbsp;
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>&nbsp;
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>&nbsp;
                    <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                </p><br>
                <p><i class="fa-solid fa-message"></i>
                    <form action="" method="Post">
                   <input type="text" name="name" size="30" placeholder="Name"  required><br>
                   <input type="email" name="email" size="30"placeholder="Email"  required><br>
                   <textarea name="message" cols="30" rows="5"placeholder="Message.."></textarea><br>
                   <input type="submit" name="send" value="send">
                    </form>
                </p>
            </div>
            <div class="col3">
                <iframe  src="https://maps.google.com/maps?q=barns%20place%20sri%20lanka&t=&z=13&ie=UTF8&iwloc=&output=embed" >
                </iframe><a href="https://embedgooglemap.net/124/"></a>
            </div>
            </div>
            <?php require "includes/footer.php"; ?>
            
        
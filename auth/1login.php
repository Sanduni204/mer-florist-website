<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>
<?php
if(isset($_POST['submit'])){
    if(empty($_POST['email']) OR empty($_POST['password']))
  echo "<script>alert('Some inputs are empty');</script>";
else{
    $email=$_POST['email'];
    $password=$_POST['password'];

    //query
    $login=$conn->query("SELECT*FROM users WHERE email='$email'");
    $login->execute();

    //fetch
    $fetch=$login->fetch(PDO::FETCH_ASSOC);

    if($login->rowCount() > 0){
        //echo $login->rowCount();
        //echo "email is valid";

        if(password_verify($password, $fetch['mypassword'])){
           $_SESSION['username'] = $fetch['username'];
           $_SESSION['email'] = $fetch['email'];
           $_SESSION['user_id'] = $fetch['id'];
           
            header("location: ".APPURL."");
            
        }else{
            echo "<script>alert('email or password is wrong');</script>";
        }
    }
    else{
        echo "<script>alert('email or password is wrong');</script>";
    }
}}
    ?>

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
    
    <div class="login-header">
        <div class="container">
                       
        </div>
    </div>

    <!-- Login Form Container -->
    <div class="login-container">
        <div class="login-form-container">
            <h2 class="login-form-title">Login</h2>
            
            <!-- Success/Error Messages -->
            <div id="login-message" class="login-message" style="display: none;">
                <!-- Messages will be displayed here -->
            </div>

            <!--form-->
            <form action="1login.php" method="POST"  id="loginForm" class="login-form">
                <div class="login-form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="login-form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                
         <button type="submit" name ="submit" class="login-btn">Login</button>
            </form>

            
        </div>
    </div>

     <?php require "../includes/footer.php"; ?>

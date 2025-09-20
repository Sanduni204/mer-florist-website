<?php require "../includes/header.php"; ?>
<?php require "../Config/config.php"; ?>
<?php
if(isset($_SESSION['username'])){
header("location: ".APPURL."1home.php");
exit;}

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
           
header("location: ".APPURL."1home.php");
        
        }else{
            echo "<script>alert('email or password is wrong');</script>";
        }
    }
    else{
        echo "<script>alert('email or password is wrong');</script>";
    }
}}
    ?>

    
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

            <div class="login-footer" style="margin-top:10px;">
                <p>Forgot password? <a href="<?php echo APPURL; ?>auth/forgot-password.php">Reset</a></p>
            </div>

            <div class="login-footer">
                <p>Don't have an account? <a href="<?php echo APPURL; ?>auth/1register.php">Sign Up</a></p>
            </div>

            
        </div>
    </div>

     <?php require "../includes/footer.php"; ?>

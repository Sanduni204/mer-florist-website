<?php 
// Start session and include config FIRST, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require "../Config/config.php";

// Check if user is already logged in and redirect BEFORE any HTML output
if(isset($_SESSION['username'])){
    // Check if user is admin and redirect accordingly
    if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true){
        header("location: ".APPURL."admin/index.php");
    } else {
        header("location: ".APPURL."1home.php");
    }
    exit;
}

// Process login form BEFORE any HTML output
if(isset($_POST['submit'])){
    if(empty($_POST['email']) OR empty($_POST['password'])){
        $error_message = "Some inputs are empty";
    } else {
        $email=$_POST['email'];
        $password=$_POST['password'];

        // Admin override: allow hardcoded admin credentials to grant admin session
        if ($email === 'admin@mer.com' && $password === 'admin123') {
            $_SESSION['is_admin'] = true;
            $_SESSION['username'] = 'Admin';
            $_SESSION['email'] = $email;
            // Optional: a synthetic user_id for admin session context
            $_SESSION['user_id'] = 0;
            
            // Check if there's a redirect URL, but admins go to admin panel
            if (isset($_SESSION['redirect_after_login'])) {
                unset($_SESSION['redirect_after_login']); // Clear it for admins
            }
            header("location: ".APPURL."admin/index.php");
            exit;
        }

        //query (prepared to avoid SQL injection)
        $login = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $login->execute([':email' => $email]);

        //fetch
        $fetch=$login->fetch(PDO::FETCH_ASSOC);

        if($login->rowCount() > 0){
            if(password_verify($password, $fetch['mypassword'])){
               $_SESSION['username'] = $fetch['username'];
               $_SESSION['email'] = $fetch['email'];
               $_SESSION['user_id'] = $fetch['id'];
               
               // Check if user is admin from database or set regular user session
               // For now, all regular users go to home page
               $_SESSION['is_admin'] = false;
               
               // Check if there's a redirect URL stored (from payment page)
               if (isset($_SESSION['redirect_after_login'])) {
                   $redirect_url = $_SESSION['redirect_after_login'];
                   unset($_SESSION['redirect_after_login']); // Clear the redirect URL
                   header("location: " . $redirect_url);
               } else {
                   header("location: ".APPURL."1home.php");
               }
               exit;
            }else{
                $error_message = "Email or password is wrong";
            }
        }
        else{
            $error_message = "Email or password is wrong";
        }
    }
}

// NOW include header after all redirect logic is complete
require "../includes/header.php";
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
            <?php if(isset($_SESSION['login_message'])): ?>
            <div id="login-message" class="login-message" style="display: block; background: #e3f2fd; color: #1565c0; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                <?php echo htmlspecialchars($_SESSION['login_message']); unset($_SESSION['login_message']); ?>
            </div>
            <?php elseif(isset($error_message)): ?>
            <div id="login-message" class="login-message" style="display: block; background: #fdecea; color: #b71c1c; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>

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

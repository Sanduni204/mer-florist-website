<?php require "../includes/header.php"; ?>
<?php require "../Config/config.php"; ?>
<?php
if(isset($_SESSION['username'])){
header("location: ".APPURL."1home.php");
exit;}

if(isset($_POST['submit'])){
        if(empty($_POST['username']) OR empty($_POST['email']) OR empty($_POST['password']))
    echo "<script>alert('Some inputs are empty');</script>";
else{
    $username=$_POST['username'];
    $email=$_POST['email'];
    $password=$_POST['password'];

    $insert=$conn->prepare("INSERT INTO users (username,email,mypassword) VALUES
    (:username, :email, :mypassword)");

    $insert->execute([
        ':username' => $username,
        ':email' => $email,
        ':mypassword' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    
    // Set a success message for login page
    $_SESSION['login_message'] = 'Registration successful! Please sign in to complete your payment.';
    header("location: 1login.php");
}
}
?>



        <div class="register-header">
            <h1></h1>
        </div>

        <!-- Registration Form Container -->
        <div class="register-container">
            <div class="register-form-container">
                <h2 class="register-form-title">Register</h2>
                
                <!-- Success/Error Messages -->
                <?php if(isset($_SESSION['register_message'])): ?>
                <div class="register-message" style="display: block; background: #e3f2fd; color: #1565c0; padding: 10px; border-radius: 6px; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($_SESSION['register_message']); unset($_SESSION['register_message']); ?>
                </div>
                <?php endif; ?>
                
                <!-- Message container for success/error messages -->
                <div id="register-message" class="register-message" style="display: none;"></div>
                

                <!--form-->
                <form id="registrationForm" action="1register.php" method ="POST">
                    <div class="register-form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="register-form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="register-form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="register-btn" name="submit">Register</button>
                </form>
                <div class="login-footer" style="margin-top: 15px;">
                    <p>Already have an account? <a href="<?php echo APPURL; ?>auth/1login.php">Sign in</a></p>
                </div>
            </div>
        </div>

    <?php require "../includes/footer.php"; ?>
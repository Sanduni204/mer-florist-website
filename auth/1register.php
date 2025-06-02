<?php require "../includes/header.php"; ?>
<?php require "../config/config.php"; ?>

<?php
if(isset($_POST['submit'])){
    if(empty($_POST['username']) OR empty($_POST['email']) OR empty($_POST['password']))
  echo "<script>alert('Some inputs are empty');<script>";
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
    header("location:login.php");
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
                
                <!-- Message container for success/error messages -->
                <div id="register-message" class="register-message" style="display: none;"></div>
                
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
            </div>
        </div>

    <?php require "../includes/footer.php"; ?>
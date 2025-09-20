<?php
session_start();
define ("APPURL","http://localhost/mer_ecommerce/");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mer</title>
<link rel="stylesheet" type="text/css" href="<?php echo APPURL; ?>/1style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>

<body>
    <header>
    
    <div class="menubar">
        <nav>
            <ul class="main">
                <li><a href="<?php echo APPURL; ?>/1home.php" id="home-link" class="navlink">Home</a></li>
                <li><a href="<?php echo APPURL; ?>/1catalogue.php" id="catalogue-link" class="navlink">Shop</a></li>
                <li><a href="<?php echo APPURL; ?>/1contact.php" id="contact-link" class="navlink">Contact</a></li>
                
                <?php if(isset($_SESSION['username'])) : ?>
                <li class="drop">
                <a href="1catalogue.php"><?php echo $_SESSION['username']; ?></a>
                <ul class="dropdown arrow-top">
                <li><a href="<?php echo APPURL; ?>/auth/logout.php">Logout</a></li>
                
            </ul>
                </li>
<?php else : ?>
    <li><a href="<?php echo APPURL; ?>/auth/1register.php" id="register-link" class="navlink">Sign-up</a></li>
    <li><a href="<?php echo APPURL; ?>/auth/1login.php" id="login-link" class="navlink">Sign-in</a></li>
    <?php endif; ?>
    <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']===true): ?>
        <li><a href="<?php echo APPURL; ?>/admin/index.php" class="navlink">Admin</a></li>
    <?php endif; ?>
                         
                    
                      
                      
                    
                <img src="<?php echo APPURL; ?>/Images/logo.png" class="logo">

            </ul>
        </nav>
    </div>
</header>
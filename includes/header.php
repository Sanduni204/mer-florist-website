<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('APPURL')) {
    define('APPURL', 'http://localhost/mer_ecommerce/');
}
// Try to load custom logo from contact_info if available
$customLogo = null;
try {
    require_once __DIR__ . '/../Config/config.php';
    $row = $conn->query('SELECT logo FROM contact_info WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['logo'])) { $customLogo = $row['logo']; }
} catch (Throwable $e) {
    // ignore if table not available
}
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
    <script src="<?php echo APPURL; ?>/1javafile.js"></script>
    </head>

<body>
    <header>
    
    <div class="menubar">
        <nav>
            <ul class="main">
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <li><a href="<?php echo APPURL; ?>admin/index.php" class="navlink">Dashboard</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/add_flower.php" class="navlink">Add Flower</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/manage_flowers.php" class="navlink">Manage Flowers</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/contact_settings.php" class="navlink">Contact Info</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/media_cleanup.php" class="navlink">Media Cleanup</a></li>
                    <li class="drop">
                        <a href="<?php echo APPURL; ?>admin/index.php">
                            <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?>
                        </a>
                        <ul class="dropdown arrow-top">
                            <li><a href="<?php echo APPURL; ?>admin/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="<?php echo APPURL; ?>/1home.php" id="home-link" class="navlink">Home</a></li>
                    <li><a href="<?php echo APPURL; ?>/1catalogue.php" id="catalogue-link" class="navlink">Shop</a></li>
                    <li><a href="<?php echo APPURL; ?>/1contact.php" id="contact-link" class="navlink">Contact</a></li>
                    
                    <?php if(isset($GLOBALS['current_page']) && $GLOBALS['current_page'] === 'search'): ?>
                    <!-- Sort dropdown only for search page -->
                    <li class="nav-sort-dropdown">
                        <div class="sort-dropdown">
                            <button class="dropdown-btn" onclick="toggleDropdown()">
                                <span id="selectedOption">Sort by</span>
                                <span class="dropdown-arrow" id="dropdownArrow">▼</span>
                            </button>
                            <div class="dropdown-content" id="dropdownContent">
                                <?php 
                                $qType = isset($GLOBALS['search_filters']['type']) ? urlencode($GLOBALS['search_filters']['type']) : '';
                                $qColor = isset($GLOBALS['search_filters']['color_theme']) ? urlencode($GLOBALS['search_filters']['color_theme']) : '';
                                ?>
                                <a href="<?php echo APPURL; ?>search.php?types=<?php echo $qType; ?>&color_theme=<?php echo $qColor; ?>&price=ASC" class="dropdown-item" id="dropdown-item-1">Price Ascending</a>
                                <a href="<?php echo APPURL; ?>search.php?types=<?php echo $qType; ?>&color_theme=<?php echo $qColor; ?>&price=DESC" class="dropdown-item" id="dropdown-item-2">Price Descending</a>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Search icon after Contact and before user links -->
                    <li class="nav-search">
                        <a href="<?php echo APPURL; ?>find.php" class="navlink" aria-label="Search bouquets">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>

                    <?php if(isset($_SESSION['username'])) : ?>
                        <li class="drop">
                            <a href="<?php echo APPURL; ?>1catalogue.php"><?php echo $_SESSION['username']; ?></a>
                            <ul class="dropdown arrow-top">
                                <li><a href="<?php echo APPURL; ?>auth/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li><a href="<?php echo APPURL; ?>auth/1register.php" id="register-link" class="navlink">Sign-up</a></li>
                        <li><a href="<?php echo APPURL; ?>auth/1login.php" id="login-link" class="navlink">Sign-in</a></li>
                    <?php endif; ?>
                <?php endif; ?>

                <img src="<?php echo APPURL; ?>/Images/<?php echo $customLogo ? htmlspecialchars($customLogo) : 'logo.png'; ?>" class="logo">
            </ul>
        </nav>
    </div>
</header>
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
        <nav class="nav-container">
            <div class="nav-left">
                <a href="<?php echo APPURL; ?>">
                    <img src="<?php echo APPURL; ?>/Images/<?php echo $customLogo ? htmlspecialchars($customLogo) : 'logo.png'; ?>" class="logo" alt="logo">
                </a>
            </div>

            <button class="mobile-toggle" aria-expanded="false" aria-label="Toggle navigation">
                <span class="hamburger"><i class="fas fa-bars"></i></span>
            </button>

            <ul class="main nav-list">
                <li class="drawer-header"><button class="drawer-close" aria-label="Close menu">&times;</button></li>
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <li><a href="<?php echo APPURL; ?>admin/index.php" class="navlink">Dashboard</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/add_flower.php" class="navlink">Add Flower</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/manage_flowers.php" class="navlink">Manage Flowers</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/manage_users.php" class="navlink">Manage Users</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/manage_messages.php" class="navlink">Messages</a></li>
                    <li><a href="<?php echo APPURL; ?>admin/contact_settings.php" class="navlink">Contact Info</a></li>
                    
                    <!-- admin username/profile removed from nav -->
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

                    <li class="nav-cart">
                        <a href="<?php echo APPURL; ?>cart.php" class="navlink" aria-label="Shopping cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php 
                            $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            if ($cartCount > 0): 
                            ?>
                                <span class="cart-count"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php if(isset($_SESSION['username'])) : ?>
                        <li>
                            <a href="<?php echo APPURL; ?>profile.php"><?php echo $_SESSION['username']; ?></a>
                        </li>
                    <?php else : ?>
                        <li><a href="<?php echo APPURL; ?>auth/1register.php" id="register-link" class="navlink">Sign-up</a></li>
                        <li><a href="<?php echo APPURL; ?>auth/1login.php" id="login-link" class="navlink">Sign-in</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
/* Remove default page margins so header sits flush to top */
html, body { margin: 0; padding: 0; }
header { margin: 0; padding: 0; }

/* Responsive navbar styles */
.nav-container{display:flex;align-items:center;justify-content:flex-start;padding:10px 18px;gap:12px;}
.nav-left .logo{height:42px;display:block}
.nav-list{list-style:none;margin:0;padding:0;display:flex;gap:12px;align-items:center}
.nav-list li{display:inline-block}
.nav-list li a{color:#000;text-decoration:none;padding:8px 10px;border-radius:4px}
.mobile-toggle{display:none;align-items:center;justify-content:center;gap:8px;background:transparent;border:1px solid transparent;padding:6px 10px;border-radius:6px;cursor:pointer}
.mobile-toggle .hamburger{font-size:1.1rem;color:#000}
/* Hide drawer close on large screens */
.drawer-close{display:none}

/* Mobile styles: side drawer */
@media (max-width: 960px){
    /* Stack ordering: show toggle at left, logo after it */
    .nav-container{position:relative;display:flex;align-items:center}
    .mobile-toggle{display:flex;order:0}
    .nav-left{order:1}
    .nav-list{display:flex;position:fixed;top:12px;left:12px;height:calc(100% - 24px);width:260px;max-width:86%;flex-direction:column;background:#fff;border:1px solid #e6e6e6;padding:12px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.12);transform:translateX(-120%);transition:transform 220ms ease;overflow:auto;z-index:1200}
    .nav-list.open{transform:translateX(0)}
    .drawer-header{list-style:none;display:flex;justify-content:flex-end;padding:4px 0;margin:0}
    .drawer-close{display:block;background:transparent;border:0;font-size:1.6rem;line-height:1;color:#444;cursor:pointer;padding:6px;border-radius:6px}
    .nav-list{align-items:flex-start}
    .nav-list li{margin:6px 0;width:100%;display:block}
    .nav-list li a{display:flex;align-items:center;justify-content:flex-start;gap:10px;text-align:left;padding:10px 8px;color:#000;border-radius:6px;width:100%}
    .nav-list li a:hover{background:#f2f2f2}
    /* Ensure all text inside the drawer is left-aligned */
    .nav-list, .nav-list * { text-align: left !important; }
    .nav-list i.fas, .nav-list i.far { min-width:20px; }
}

/* Ensure toggle and links are left-aligned on large screens */
@media (min-width: 961px){
    .mobile-toggle{order:0}
    .nav-left{order:1;margin-left:8px}
    .nav-list{order:2;margin-left:12px}
}

/* Ensure dropdown items look good */
.nav-list li a{background:transparent;color:#000}
.nav-list li .dropdown, .nav-list li .dropdown-content{background:#f5f5f5}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const toggle = document.querySelector('.mobile-toggle');
    const navList = document.querySelector('.nav-list');
    if(!toggle || !navList) return;
    toggle.addEventListener('click', function(e){
        const expanded = navList.classList.toggle('open');
        toggle.setAttribute('aria-expanded', expanded);
    });

    // Close button inside drawer
    const drawerClose = document.querySelector('.drawer-close');
    if (drawerClose) {
        drawerClose.addEventListener('click', function(){
            navList.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    }

    // Close nav when clicking outside (mobile)
    document.addEventListener('click', function(e){
        if(window.innerWidth <= 960){
            if(!e.target.closest('.nav-container')){
                navList.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
});
</script>
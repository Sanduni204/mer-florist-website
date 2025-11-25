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
                <button class="drawer-close" aria-label="Close menu">&times;</button>
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
    /* Hide the nav by default on mobile to avoid it rendering inline under the header.
       When the menu is opened the `.open` class makes it visible and slides it in. */
    .nav-list{
        display: none;
        position: fixed;
        top: 12px;
        left: 12px !important;
        height: calc(100% - 24px);
        width: 260px !important;
        max-width: 86%;
        box-sizing: border-box;
        flex-direction: column;
        background: #fff;
        border: 1px solid #e6e6e6;
        padding: 12px;
        /* reserve space at the top for the absolute-positioned close button (inside the box) */
        padding-top: 56px;
        padding-bottom: 20px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateX(-120%);
        transition: transform 220ms ease, opacity 180ms ease;
        overflow: auto;
        z-index: 1200;
    }
     /* Use a consistent vertical gap between drawer items on mobile and
         ensure no per-item margins interfere. This makes all nav rows
         (Sort by, Home, Shop, etc.) share the exact same spacing. */
     .nav-list { gap: 12px; row-gap: 12px; column-gap: 0; }
     /* Remove any leftover margins on direct children so gap is the sole spacer */
     .nav-list > * { margin: 0 !important; }
     /* Ensure list items take full width for consistent visual blocks */
     .nav-list li { width: 100% !important; }
    /* Make all drawer rows the same height so vertical gaps look equal */
    /* Ensure the list item itself is the fixed-height row so children fill it
       — this prevents inline elements inside the button from changing the row height. */
    .nav-list > li { height: 48px !important; box-sizing: border-box; }
    .nav-list > li > a { height: 100% !important; display: flex; align-items: center; padding: 0 10px !important; }
    /* Sort dropdown structure: li > .sort-dropdown > .dropdown-btn */
    .nav-list > li.nav-sort-dropdown .sort-dropdown { height: 100%; }
    .nav-list > li.nav-sort-dropdown .dropdown-btn { height: 100% !important; display: flex; align-items: center; padding: 0 10px !important; width: 100% !important; }
    /* Standardize icon sizing so icon-only rows match text rows visually */
    .nav-list i.fas, .nav-list i.far, .nav-list i {
        font-size: 20px;
        line-height: 1;
        display: inline-block;
        min-width: 24px; /* reserve space so text and icons align */
        margin-right: 10px;
        text-align: center;
    }
    /* Keep the cart badge vertically centered relative to the 48px row */
    .nav-cart .cart-count {
        top: 12px;
        right: 12px;
    }
    .nav-list.open{display:flex;transform:translateX(0);}
    /* Place close button at the right inside the drawer (mobile) */
     /* keep the header element in the DOM (flow) and absolutely position the close icon
         relative to the drawer so it sits inside the box at the top-right. */
     .drawer-header{list-style:none;padding:0;margin:0;position:relative;height:0}
    .nav-list .drawer-close{position:absolute;top:12px;right:12px;z-index:1301;display:block;background:transparent;border:0;font-size:1.6rem;line-height:1;color:#444;cursor:pointer;padding:8px;border-radius:6px}
    /* Place Sort dropdown directly under the close button and align it left */
    /* ensure Sort dropdown appears directly below the header area and is left-aligned */
    .nav-sort-dropdown{order:1;width:100%;display:flex;align-items:center;padding:0;margin-top:0}
    .nav-sort-dropdown .sort-dropdown{width:100%}
    /* Override global fixed positioning (from 1style.css) so sort dropdown
       participates in the drawer flow on mobile. Use !important to counter
       the earlier rules that set position:fixed and left/top with !important. */
    .nav-sort-dropdown {
        position: static !important;
        top: auto !important;
        left: auto !important;
        z-index: auto !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .nav-sort-dropdown .sort-dropdown { position: static !important; }
    /* match other drawer items' left padding so 'Sort by' aligns exactly */
     .nav-sort-dropdown .dropdown-btn{width:100%;text-align:left;padding:10px 8px;border-radius:6px;background:transparent}
    .nav-list{align-items:flex-start}
    /* Put drawer header first (0), sort dropdown second (1), others after (2) */
    .nav-list li{margin:0;width:100%;display:block;order:2}
    .nav-list .drawer-header{order:0}
    .nav-list .nav-sort-dropdown{order:1}
    /* Use a consistent left padding on the list so all items (including Sort) start
       at the same x position without relying on absolute/relative offsets. */
    .nav-list { padding-left: 12px !important; }
    /* Keep anchors and the dropdown button positioned normally so they align
       to the UL's left padding rather than using individual left offsets. */
    /* Apply one consistent rule so all drawer items share the same look. */
    .nav-list li a,
    .nav-sort-dropdown .dropdown-btn {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        text-align: left;
        /* enforce fixed row height and remove vertical padding so all rows match */
        height: 48px !important;
        min-height: 48px !important;
        padding: 0 10px !important;
        color: #000;
        border-radius: 6px;
        width: 100% !important;
        margin-left: 0 !important;
        background: transparent;
        border: 0;
        cursor: pointer;
        font-style: italic;
    }
    .nav-list li a:hover,
    .nav-sort-dropdown .dropdown-btn:hover { background: #f2f2f2; }
    /* Ensure all text inside the drawer is left-aligned */
    .nav-list, .nav-list * { text-align: left !important; }
    .nav-list i.fas, .nav-list i.far { min-width:20px; }
}

/* Ensure toggle and links are left-aligned on large screens */
@media (min-width: 961px){
    .mobile-toggle{order:0}
    /* On desktop place nav items first (left) and push logo to the right */
    .nav-list{order:1;align-items:center;gap:12px}
    .nav-left{order:2;margin-left:auto}
     /* Keep the container padding modest so items sit predictably from the left
         On desktop we remove the container left padding and let the UL control the column.
         This avoids double offsets from both the container and the list. */
     .nav-container{padding-left:0}
     /* Make the nav items align on the same left column: give the list a single left offset
         and remove extra left padding from individual items so their text starts together. */
    /* Ensure nav items and dropdown use the same padding and alignment */
    /* UL controls the left column to align text; items have zero left padding */
    .nav-list { padding-left: 18px !important; }
    .nav-list li a,
    .nav-sort-dropdown .dropdown-btn {
        display: inline-flex;
        align-items: center;
        padding: 8px 10px 8px 0 !important; /* left = 0 so text aligns with UL */
        margin: 0 !important;
        height: 42px; /* match logo height for visual alignment */
        line-height: 1;
        background: transparent;
        border: 0;
        color: #000;
        text-align: left;
        cursor: pointer;
        font-style: italic;
    }
    /* Remove extra offsets from list items */
    .nav-list li { margin-left: 0 !important; }
    .nav-sort-dropdown { margin-left: 0 !important; }
    .nav-sort-dropdown { display: flex; align-items: center; }
    .nav-list, .nav-list * { text-align: left; }
    /* Override global fixed positioning (from 1style.css) on desktop so
       the Sort dropdown participates in the nav flow and aligns with
       other nav items. We use !important to beat the earlier rules. */
    .nav-sort-dropdown {
        position: static !important;
        top: auto !important;
        left: auto !important;
        margin-left: 0 !important;
        width: auto !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .nav-sort-dropdown .dropdown-btn { padding-left: 0 !important; }
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

// Ensure page content is not hidden behind the fixed nav.
// Set body's padding-top equal to the actual nav height and update on resize/load.
(function(){
    function adjustBodyPadding(){
        var nav = document.querySelector('nav');
        if(!nav) return;
        // On small screens we don't want extra body padding (removes top gap)
        if (window.innerWidth <= 960) {
            if (document.body.style.paddingTop !== '0px') {
                document.body.style.paddingTop = '0px';
            }
            return;
        }
        // Use offsetHeight to include padding and borders on larger screens
        var h = nav.offsetHeight || 0;
        // Only update if different to avoid layout thrash
        if (document.body.style.paddingTop !== h + 'px') {
            document.body.style.paddingTop = h + 'px';
        }
    }
    window.addEventListener('resize', adjustBodyPadding);
    window.addEventListener('load', adjustBodyPadding);
    // Run after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', adjustBodyPadding);
    } else {
        adjustBodyPadding();
    }
})();
</script>
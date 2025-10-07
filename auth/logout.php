<?php
session_start();
session_unset();
session_destroy();

// Define APPURL if not defined
if (!defined('APPURL')) {
    define('APPURL', 'http://localhost/mer_ecommerce/');
}

header("location: " . APPURL . "1home.php");

exit();

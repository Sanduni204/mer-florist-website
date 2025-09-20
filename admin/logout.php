<?php
session_start();
// Ensure APPURL is available for redirects
if (!defined('APPURL')) {
	define('APPURL', 'http://localhost/mer_ecommerce/');
}
unset($_SESSION['is_admin'], $_SESSION['admin_email']);
header('Location: ' . APPURL);
exit;

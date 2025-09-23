<?php
session_start();
// Ensure APPURL is available for redirects
if (!defined('APPURL')) {
	define('APPURL', 'http://localhost/mer_ecommerce/');
}
// Clear all session data (admin + normal user)
$_SESSION = [];
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_unset();
session_destroy();

// Send everyone to the public home page after logout
header('Location: ' . APPURL . '1home.php');
exit;

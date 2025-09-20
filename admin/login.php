<?php
session_start();
require_once __DIR__ . '/../Config/config.php';
if (!defined('APPURL')) {
  define('APPURL', 'http://localhost/mer_ecommerce/');
}

// Simple hardcoded admin credentials (replace with DB users later if needed)
$ADMIN_USER = 'admin@mer.com';
$ADMIN_PASS = 'admin123';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  if ($email === $ADMIN_USER && $password === $ADMIN_PASS) {
    $_SESSION['is_admin'] = true;
    $_SESSION['admin_email'] = $email;
    header('Location: ' . APPURL . 'admin/index.php');
    exit;
  } else {
    $error = 'Invalid admin credentials';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" type="text/css" href="<?php echo APPURL; ?>/1style.css?v=<?php echo time(); ?>">
  <style>
    .admin-login { max-width: 420px; margin: 120px auto; background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .admin-login h2 { margin: 0 0 16px; }
    .admin-login label { display:block; margin: 10px 0 6px; }
    .admin-login input { width:100%; padding: 10px; border:1px solid #ccc; border-radius:6px; }
    .admin-login button { margin-top:12px; width:100%; padding:12px; border:none; border-radius:8px; background:#222; color:#fff; cursor:pointer; }
    .error { background:#fdecea; color:#b71c1c; padding:10px; border-radius:6px; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="admin-login">
    <h2>Admin Login</h2>
    <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />

      <button type="submit">Sign in</button>
    </form>
  </div>
</body>
</html>

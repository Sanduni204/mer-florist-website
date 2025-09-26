<?php require "../includes/header.php"; ?>
<?php require "../Config/config.php"; ?>
<?php
// Ensure reset columns exist (in case this page is accessed first)
try {
    $conn->query("SELECT password_reset_token, password_reset_expires FROM users LIMIT 1");
} catch (PDOException $e) {
    try { $conn->exec("ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(128) NULL"); } catch (PDOException $e2) {}
    try { $conn->exec("ALTER TABLE users ADD COLUMN password_reset_expires DATETIME NULL"); } catch (PDOException $e3) {}
}

// Basic guards and load user by token/email
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

$valid = false;
$user = null;
if ($token !== '' && $email !== '') {
    try {
        // Match by both email and token directly, then check expiry
        $stmt = $conn->prepare("SELECT id, email, password_reset_expires FROM users WHERE email = :email AND password_reset_token = :token LIMIT 1");
        $stmt->execute([':email' => $email, ':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && !empty($user['password_reset_expires']) && strtotime($user['password_reset_expires']) > time()) {
            $valid = true;
        }
    } catch (PDOException $e) {
        $valid = false;
    }
}

$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (strlen($pass) < 6) {
        $message = 'Password must be at least 6 characters.';
    } elseif ($pass !== $confirm) {
        $message = 'Passwords do not match.';
    } else {
        // Verify token again before updating
        $stmt = $conn->prepare("SELECT id, password_reset_expires FROM users WHERE email = :email AND password_reset_token = :token LIMIT 1");
        $stmt->execute([':email' => $email, ':token' => $token]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($u && !empty($u['password_reset_expires']) && strtotime($u['password_reset_expires']) > time()) {
            $upd = $conn->prepare("UPDATE users SET mypassword = :p, password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id");
            $upd->execute([
                ':p' => password_hash($pass, PASSWORD_DEFAULT),
                ':id' => $u['id']
            ]);
            $success = true;
        } else {
            $message = 'Reset link is invalid or has expired. Please request a new link.';
        }
    }
}
?>

<div class="login-header">
    <div class="container"></div>
</div>

<div class="login-container">
    <div class="login-form-container">
        <h2 class="login-form-title">Reset Password</h2>

        <?php if ($success): ?>
            <div class="login-message" style="display:block;">
                Your password has been reset successfully. <a href="<?php echo APPURL; ?>auth/1login.php">Sign in</a>
            </div>
        <?php else: ?>
            <?php if (!$valid && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                <div class="login-message" style="display:block;">Reset link is invalid or has expired. <a href="<?php echo APPURL; ?>auth/forgot-password.php">Request a new link</a>.</div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="login-message" style="display:block;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($valid || $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <form action="<?php echo APPURL; ?>auth/reset-password.php" method="POST" class="login-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="login-form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter new password">
                </div>
                <div class="login-form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password">
                </div>
                <button type="submit" class="login-btn">Reset Password</button>
            </form>
            <?php endif; ?>
        <?php endif; ?>

        <div class="login-footer" style="margin-top:10px;">
            <p><a href="<?php echo APPURL; ?>auth/1login.php">Back to Sign In</a></p>
        </div>
    </div>
</div>

<?php require "../includes/footer.php"; ?>

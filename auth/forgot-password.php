<?php require "../includes/header.php"; ?>
<?php require "../Config/config.php"; ?>
<?php
// Simple helper to ensure columns exist (dev convenience)
function ensure_reset_columns(PDO $conn) {
	try {
		$conn->query("SELECT password_reset_token, password_reset_expires FROM users LIMIT 1");
		return; // Columns exist
	} catch (PDOException $e) {
		// Try adding columns individually
		try { $conn->exec("ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(128) NULL"); } catch (PDOException $e2) {}
		try { $conn->exec("ALTER TABLE users ADD COLUMN password_reset_expires DATETIME NULL"); } catch (PDOException $e3) {}
	}
}

$message = '';
$link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = isset($_POST['email']) ? trim($_POST['email']) : '';
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = 'Please enter a valid email address.';
	} else {
		// Ensure schema supports reset fields
		ensure_reset_columns($conn);

		// Check user exists
		$stmt = $conn->prepare("SELECT id, email FROM users WHERE email = :email LIMIT 1");
		$stmt->execute([':email' => $email]);
		$user = $stmt->fetch();

		// Always generate token to keep behavior consistent (avoid enumeration)
		$token = bin2hex(random_bytes(32));
		$expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

		if ($user) {
			$upd = $conn->prepare("UPDATE users SET password_reset_token = :t, password_reset_expires = :e WHERE id = :id");
			$upd->execute([':t' => $token, ':e' => $expiresAt, ':id' => $user['id']]);
		}

		// Build reset link (would be emailed in production). For dev, display it.
		$base = defined('APPURL') ? APPURL : 'http://localhost/mer_ecommerce/';
		$link = $base . 'auth/reset-password.php?token=' . urlencode($token) . '&email=' . urlencode($email);
		$message = 'If the email exists in our system, a reset link has been generated.';
	}
}
?>

<div class="login-header">
	<div class="container"></div>
	</div>

<div class="login-container">
	<div class="login-form-container">
		<h2 class="login-form-title">Forgot Password</h2>

		<?php if (!empty($message)) : ?>
			<div class="login-message" style="display:block;">
				<?php echo htmlspecialchars($message); ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($link)) : ?>
			<div class="login-message" style="display:block;">
				Dev shortcut: <a href="<?php echo $link; ?>">Reset your password</a>
			</div>
		<?php endif; ?>

		<form action="forgot-password.php" method="POST" class="login-form">
			<div class="login-form-group">
				<label for="email">Enter your email</label>
				<input type="email" id="email" name="email" required placeholder="your@email.com">
			</div>
			<button type="submit" class="login-btn">Send Reset Link</button>
		</form>

		<div class="login-footer" style="margin-top:10px;">
			<p>Remembered it? <a href="<?php echo APPURL; ?>auth/1login.php">Back to Sign In</a></p>
		</div>
	</div>
</div>

<?php require "../includes/footer.php"; ?>


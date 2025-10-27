<?php require __DIR__ . '/admin_header.php'; ?>
<?php
// Manage Users - list users and allow deletion
$success = '';
$error = '';

// Ensure a CSRF token exists for POST actions
if (!isset($_SESSION['csrf_token'])) {
  try { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); } catch (Throwable $e) { $_SESSION['csrf_token'] = bin2hex((string)mt_rand()); }
}

// Handle delete action (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $token = $_POST['csrf_token'] ?? '';
  if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    $error = 'Invalid request. Please try again.';
  } else {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      $error = 'Invalid user identifier.';
    } else {
      // Prevent accidental deletion of currently logged-in user (if applicable)
      if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $id) {
        $error = 'You cannot delete the account you are currently logged into.';
      } else {
        try {
          // Fetch profile image to remove file
          $stmt = $conn->prepare('SELECT profile_image FROM users WHERE id = :id');
          $stmt->execute([':id' => $id]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          $del = $conn->prepare('DELETE FROM users WHERE id = :id');
          $del->execute([':id' => $id]);
          if ($del->rowCount() > 0) {
            if (!empty($row['profile_image']) && strpos($row['profile_image'], 'Images/avatars/') === 0) {
              $path = __DIR__ . '/../' . $row['profile_image'];
              if (is_file($path)) { @unlink($path); }
            }
            $success = 'User deleted successfully.';
          } else {
            $error = 'User not found or already deleted.';
          }
        } catch (Throwable $e) {
          $error = 'Failed to delete user.';
        }
      }
    }
  }
}

$rows = [];
try {
  $rows = $conn->query('SELECT id, username, email, profile_image FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $error = 'Failed to fetch users.';
}
?>
<?php if (!empty($success)): ?><div class="register-message success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="register-message error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Avatar</th>
      <th>Username</th>
      <th>Email</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo (int)$r['id']; ?></td>
        <td><?php if (!empty($r['profile_image'])): ?><img src="<?php echo APPURL . $r['profile_image']; ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;" /><?php endif; ?></td>
        <td><?php echo htmlspecialchars($r['username']); ?></td>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td>
          <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete this user? This cannot be undone.');">
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />
            <button type="submit" style="background:none;border:none;color:#c0392b;cursor:pointer;text-decoration:underline;">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require __DIR__ . '/admin_footer.php'; ?>

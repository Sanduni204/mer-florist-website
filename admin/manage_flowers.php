<?php require __DIR__ . '/admin_header.php'; ?>
<?php
// Feedback messages
$success = '';
$error = '';
// Detect primary key of shop table dynamically
$pk = 'id';
try {
  $cols = $conn->query('SHOW COLUMNS FROM shop')->fetchAll(PDO::FETCH_ASSOC);
  if ($cols) {
    foreach ($cols as $c) {
      if (!empty($c['Key']) && strtoupper($c['Key']) === 'PRI') { $pk = $c['Field']; break; }
    }
    // Fallback if PRI not found: prefer 'id' or 'ID' or first column
    if (!isset($pk) || $pk === null) { $pk = 'id'; }
    $fields = array_column($cols, 'Field');
    if (!in_array($pk, $fields, true)) {
      if (in_array('id', $fields, true)) { $pk = 'id'; }
      elseif (in_array('ID', $fields, true)) { $pk = 'ID'; }
      else { $pk = $fields[0]; }
    }
  }
} catch (Throwable $e) {
  // Keep default 'id' if SHOW COLUMNS fails
}

// Ensure a CSRF token exists for POST actions
if (!isset($_SESSION['csrf_token'])) {
  try { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); } catch (Throwable $e) { $_SESSION['csrf_token'] = bin2hex((string)mt_rand()); }
}

// Handle delete action (POST only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $token = $_POST['csrf_token'] ?? '';
  if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    $error = 'Invalid request. Please try again.';
  } else {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
      $error = 'Invalid flower identifier.';
    } else {
      try {
        // Fetch image name to delete from disk after DB deletion
        $stmt = $conn->prepare("SELECT image FROM shop WHERE $pk = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the row
        $del = $conn->prepare("DELETE FROM shop WHERE $pk = :id");
        $del->execute([':id' => $id]);

        if ($del->rowCount() > 0) {
          // Remove image file if present
          if (!empty($row['image'])) {
            $imgPath = __DIR__ . '/../Images/' . $row['image'];
            if (is_file($imgPath)) { @unlink($imgPath); }
          }
          $success = 'Flower deleted successfully.';
        } else {
          $error = 'Flower not found or already deleted.';
        }
      } catch (Throwable $e) {
        $error = 'Failed to delete flower.';
      }
    }
  }
}

$sql = "SELECT $pk AS id, fid, name, type, color_theme, price, image, description FROM shop ORDER BY $pk DESC";
$rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Manage Flowers</h2>
<?php if (!empty($success)): ?><div class="register-message success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($error)):   ?><div class="register-message error"><?php   echo htmlspecialchars($error);   ?></div><?php endif; ?>
<table class="admin-table">
  <thead>
    <tr>
      <th>FID</th>
      <th>Image</th>
      <th>Name</th>
      <th>Type</th>
      <th>Color</th>
      <th>Price</th>
      <th>Description</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['fid'] ?? ''); ?></td>
        <td><?php if (!empty($r['image'])): ?><img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($r['image']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:6px;" /><?php endif; ?></td>
        <td><?php echo htmlspecialchars($r['name']); ?></td>
        <td><?php echo htmlspecialchars($r['type']); ?></td>
        <td><?php echo htmlspecialchars($r['color_theme']); ?></td>
        <td>Rs. <?php echo number_format((float)$r['price'], 2); ?></td>
        <td>
          <?php $desc = trim((string)($r['description'] ?? '')); ?>
          <div style="max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($desc); ?>">
            <?php echo htmlspecialchars($desc); ?>
          </div>
        </td>
        <td>
            <a href="<?php echo APPURL; ?>admin/edit_flower.php?id=<?php echo (int)$r['id']; ?>">Edit</a>
            <form method="post" style="display:inline-block;margin-left:8px;" onsubmit="return confirm('Are you sure you want to delete this flower?');">
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

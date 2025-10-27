<?php require __DIR__ . '/admin_header.php'; ?>
<?php
// Initialize vars
$error = '';
$success = '';
// Detect primary key name
$pk = 'id';
try {
  $cols = $conn->query('SHOW COLUMNS FROM shop')->fetchAll(PDO::FETCH_ASSOC);
  if ($cols) {
    foreach ($cols as $c) { if (!empty($c['Key']) && strtoupper($c['Key']) === 'PRI') { $pk = $c['Field']; break; } }
    $fields = array_column($cols, 'Field');
    if (!in_array($pk, $fields, true)) {
      if (in_array('id', $fields, true)) { $pk = 'id'; }
      elseif (in_array('ID', $fields, true)) { $pk = 'ID'; }
      else { $pk = $fields[0]; }
    }
  }
} catch (Throwable $e) { /* default to id */ }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  $error = 'Invalid flower ID.';
} else {
  // Fetch existing row using dynamic PK
  $stmt = $conn->prepare("SELECT * FROM shop WHERE $pk = :id");
  $stmt->execute([':id' => $id]);
  $flower = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$flower) {
    $error = 'Flower not found.';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
  $name = trim($_POST['name'] ?? '');
  $type = trim($_POST['type'] ?? '');
  $color_theme = trim($_POST['color_theme'] ?? '');
  $price = trim($_POST['price'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $fid = trim($_POST['fid'] ?? '');
  $currentImage = $flower['image'] ?? '';
  $newImageFileName = $currentImage; // default keep current

  if ($name === '' || $type === '' || $color_theme === '' || $price === '') {
    $error = 'Please fill in all required fields (name, type, color theme, price).';
  } elseif (!is_numeric($price)) {
    $error = 'Price must be a number.';
  } else {
    // If a new image is uploaded, process it
    if (!empty($_FILES['image']['name'])) {
      $uploadDir = __DIR__ . '/../Images/';
      if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name));
      $newImageFileName = $safeBase . '_' . time() . '.' . $ext;
      $targetPath = $uploadDir . $newImageFileName;

      if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $error = 'Failed to upload image.';
      } else {
        // Optional: remove old image if exists and different
        if (!empty($currentImage)) {
          $oldPath = $uploadDir . $currentImage;
          if (is_file($oldPath)) { @unlink($oldPath); }
        }
      }
    }

    if ($error === '') {
      $sql = "UPDATE shop SET fid = :fid, name = :name, type = :type, color_theme = :color_theme, price = :price, description = :description, image = :image WHERE $pk = :id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':fid' => ($fid !== '' ? $fid : null),
        ':name' => $name,
        ':type' => $type,
        ':color_theme' => $color_theme,
        ':price' => (float)$price,
        ':description' => $description,
        ':image' => $newImageFileName,
        ':id' => $id,
      ]);
      $success = 'Flower updated successfully!';
      // Refresh $flower
      $stmt = $conn->prepare("SELECT * FROM shop WHERE $pk = :id");
      $stmt->execute([':id' => $id]);
      $flower = $stmt->fetch(PDO::FETCH_ASSOC);
    }
  }
}
?>
 
<?php if ($error): ?>
  <div class="register-message error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="register-message success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if (!$error && $flower): ?>
<form class="admin-form" method="post" enctype="multipart/form-data">
  <label for="fid">FID (optional)</label>
  <input id="fid" name="fid" value="<?php echo htmlspecialchars($flower['fid'] ?? ''); ?>" placeholder="e.g., F-001" />

  <label for="name">Name</label>
  <input id="name" name="name" value="<?php echo htmlspecialchars($flower['name'] ?? ''); ?>" required />

  <label for="type">Type</label>
  <input id="type" name="type" value="<?php echo htmlspecialchars($flower['type'] ?? ''); ?>" required />

  <label for="color_theme">Color Theme</label>
  <input id="color_theme" name="color_theme" value="<?php echo htmlspecialchars($flower['color_theme'] ?? ''); ?>" required />

  <label for="price">Price (Rs)</label>
  <input id="price" name="price" type="number" step="0.01" min="0" value="<?php echo htmlspecialchars($flower['price'] ?? ''); ?>" required />

  <label for="description">Description</label>
  <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($flower['description'] ?? ''); ?></textarea>

  


  <label>Current Image</label>
  <div style="margin-bottom:10px;">
    <?php if (!empty($flower['image'])): ?>
      <img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($flower['image']); ?>" alt="Current Image" style="width:100px;height:100px;object-fit:cover;border-radius:6px;" />
    <?php else: ?>
      <em>No image</em>
    <?php endif; ?>
  </div>

  <label for="image">Replace Image (optional)</label>
  <input id="image" name="image" type="file" accept="image/*" />

  <button type="submit">Save Changes</button>
  <a href="<?php echo APPURL; ?>admin/manage_flowers.php" style="margin-left:10px;">Back to List</a>
</form>
<?php endif; ?>
<?php require __DIR__ . '/admin_footer.php'; ?>

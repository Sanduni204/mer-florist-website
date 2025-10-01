<?php require __DIR__ . '/admin_header.php'; ?>
<?php
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $type = trim($_POST['type'] ?? '');
  $color_theme = trim($_POST['color_theme'] ?? '');
  $price = trim($_POST['price'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $fid = trim($_POST['fid'] ?? '');
  $imageFileName = '';

  if ($name === '' || $type === '' || $color_theme === '' || $price === '') {
    $error = 'Please fill in all required fields (name, type, color theme, price).';
  } elseif (!is_numeric($price)) {
    $error = 'Price must be a number.';
  } else {
    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
      $uploadDir = __DIR__ . '/../Images/';
      if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name));
      $imageFileName = $safeBase . '_' . time() . '.' . $ext;
      $targetPath = $uploadDir . $imageFileName;
      if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $error = 'Failed to upload image.';
      }
    }

    if ($error === '') {
      // Include fid when available
      $sql = "INSERT INTO shop (fid, name, type, color_theme, price, description, image) VALUES (:fid, :name, :type, :color_theme, :price, :description, :image)";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':fid' => ($fid !== '' ? $fid : null),
        ':name' => $name,
        ':type' => $type,
        ':color_theme' => $color_theme,
        ':price' => (float)$price,
        ':description' => $description,
        ':image' => $imageFileName,
      ]);
      $success = 'Flower added successfully!';
    }
  }
}
?>
<h2>Add New Flower</h2>
<?php if ($success): ?><div class="register-message success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="register-message error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form class="admin-form" method="post" enctype="multipart/form-data">
  <label for="fid">FID (optional)</label>
  <input id="fid" name="fid" placeholder="e.g., F-001" />

  <label for="name">Name</label>
  <input id="name" name="name" required />

  <label for="type">Type</label>
  <input id="type" name="type" placeholder="Rose, Lily, Daisy, ..." required />

  <label for="color_theme">Color Theme</label>
  <input id="color_theme" name="color_theme" placeholder="Red, Pink, Yellow, ..." required />

  <label for="price">Price (Rs)</label>
  <input id="price" name="price" type="number" step="0.01" min="0" required />

  <label for="description">Description</label>
  <textarea id="description" name="description" rows="3" placeholder="Optional details..."></textarea>



  <label for="image">Image (optional)</label>
  <input id="image" name="image" type="file" accept="image/*" />

  <button type="submit">Add Flower</button>
</form>
<?php require __DIR__ . '/admin_footer.php'; ?>

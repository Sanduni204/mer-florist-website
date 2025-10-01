<?php require __DIR__ . '/admin_header.php'; ?>
<?php
$success = '';
$error = '';

// Ensure contact_info table exists (single-row settings by id=1)
try {
  $conn->exec("CREATE TABLE IF NOT EXISTS contact_info (
    id INT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    instagram VARCHAR(255) NULL,
    facebook VARCHAR(255) NULL,
    twitter VARCHAR(255) NULL,
    youtube VARCHAR(255) NULL,
    whatsapp VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    map_embed_url TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  // Ensure 'logo' column exists if table was older
  $cols = $conn->query('SHOW COLUMNS FROM contact_info')->fetchAll(PDO::FETCH_ASSOC);
  $hasLogo = false; foreach ($cols as $c) { if (strcasecmp($c['Field'], 'logo') === 0) { $hasLogo = true; break; } }
  if (!$hasLogo) { $conn->exec('ALTER TABLE contact_info ADD COLUMN logo VARCHAR(255) NULL'); }
} catch (Throwable $e) { /* ignore create errors; select will fallback */ }

// Load existing or insert default row
$contact = [
  'id' => 1,
  'address' => 'Barnes Pl, Colombo 07',
  'email' => 'mer_shopping@gmail.com',
  'phone' => '0112345678',
  'instagram' => '#',
  'facebook' => '#',
  'twitter' => '#',
  'youtube' => '#',
  'whatsapp' => '#',
  'logo' => null,
  'map_embed_url' => 'https://maps.google.com/maps?q=barns%20place%20sri%20lanka&t=&z=13&ie=UTF8&iwloc=&output=embed',
];

try {
  $row = $conn->query('SELECT * FROM contact_info WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
  if ($row) { $contact = array_merge($contact, $row); }
  else {
    $ins = $conn->prepare('INSERT INTO contact_info (id, address, email, phone, instagram, facebook, twitter, youtube, whatsapp, logo, map_embed_url) VALUES (1, :address, :email, :phone, :instagram, :facebook, :twitter, :youtube, :whatsapp, :logo, :map)');
    $ins->execute([
      ':address' => $contact['address'],
      ':email' => $contact['email'],
      ':phone' => $contact['phone'],
      ':instagram' => $contact['instagram'],
      ':facebook' => $contact['facebook'],
      ':twitter' => $contact['twitter'],
      ':youtube' => $contact['youtube'],
      ':whatsapp' => $contact['whatsapp'],
      ':logo' => $contact['logo'],
      ':map' => $contact['map_embed_url'],
    ]);
  }
} catch (Throwable $e) { /* if table missing, page will still render defaults */ }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $address = trim($_POST['address'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $instagram = trim($_POST['instagram'] ?? '');
  $facebook = trim($_POST['facebook'] ?? '');
  $twitter = trim($_POST['twitter'] ?? '');
  $youtube = trim($_POST['youtube'] ?? '');
  $whatsapp = trim($_POST['whatsapp'] ?? '');
  $map = trim($_POST['map_embed_url'] ?? '');
  $newLogoFile = null;

  if ($address === '' || $email === '' || $phone === '') {
    $error = 'Address, Email, and Phone are required.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please provide a valid email address.';
  } else {
    // Handle logo upload if provided
    if (!empty($_FILES['logo']['name'])) {
      $uploadDir = __DIR__ . '/../Images/';
      if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
      $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
      $allowed = ['png','jpg','jpeg','gif','webp','svg'];
      if (!in_array($ext, $allowed, true)) {
        $error = 'Invalid logo file type.';
      } else {
        $safeBase = 'logo_'.time();
        $newLogoFile = $safeBase . '.' . $ext;
        $target = $uploadDir . $newLogoFile;
        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
          $error = 'Failed to upload logo.';
        }
      }
    }
    try {
      // Determine logo value to save: new upload or keep existing
      $logoToSave = $newLogoFile !== null ? $newLogoFile : ($contact['logo'] ?? null);
      $stmt = $conn->prepare('REPLACE INTO contact_info (id, address, email, phone, instagram, facebook, twitter, youtube, whatsapp, logo, map_embed_url) VALUES (1, :address, :email, :phone, :instagram, :facebook, :twitter, :youtube, :whatsapp, :logo, :map)');
      $stmt->execute([
        ':address' => $address,
        ':email' => $email,
        ':phone' => $phone,
        ':instagram' => ($instagram !== '' ? $instagram : null),
        ':facebook' => ($facebook !== '' ? $facebook : null),
        ':twitter' => ($twitter !== '' ? $twitter : null),
        ':youtube' => ($youtube !== '' ? $youtube : null),
        ':whatsapp' => ($whatsapp !== '' ? $whatsapp : null),
        ':logo' => $logoToSave,
        ':map' => ($map !== '' ? $map : null),
      ]);
      $success = 'Contact information saved successfully.';
      $contact = array_merge($contact, [
        'address' => $address,
        'email' => $email,
        'phone' => $phone,
        'instagram' => $instagram,
        'facebook' => $facebook,
        'twitter' => $twitter,
        'youtube' => $youtube,
        'whatsapp' => $whatsapp,
        'logo' => $logoToSave,
        'map_embed_url' => $map,
      ]);
    } catch (Throwable $e) {
      $error = 'Failed to save contact information.';
    }
  }
}
?>
<h2>Contact Information</h2>
<?php if (!empty($success)): ?><div class="register-message success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="register-message error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<form class="admin-form" method="post" enctype="multipart/form-data">
  <label for="address">Address</label>
  <input id="address" name="address" value="<?php echo htmlspecialchars($contact['address'] ?? ''); ?>" required />

  <label for="email">Email</label>
  <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>" required />

  <label for="phone">Phone</label>
  <input id="phone" name="phone" value="<?php echo htmlspecialchars($contact['phone'] ?? ''); ?>" required />

  <label for="instagram">Instagram URL</label>
  <input id="instagram" name="instagram" value="<?php echo htmlspecialchars($contact['instagram'] ?? ''); ?>" />

  <label for="facebook">Facebook URL</label>
  <input id="facebook" name="facebook" value="<?php echo htmlspecialchars($contact['facebook'] ?? ''); ?>" />

  <label for="twitter">Twitter URL</label>
  <input id="twitter" name="twitter" value="<?php echo htmlspecialchars($contact['twitter'] ?? ''); ?>" />

  <label for="youtube">YouTube URL</label>
  <input id="youtube" name="youtube" value="<?php echo htmlspecialchars($contact['youtube'] ?? ''); ?>" />

  <label for="whatsapp">WhatsApp URL</label>
  <input id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($contact['whatsapp'] ?? ''); ?>" />

  <label>Current Logo</label>
  <div style="margin-bottom:10px;">
    <?php if (!empty($contact['logo'])): ?>
      <img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($contact['logo']); ?>" alt="Logo" style="height:60px;object-fit:contain;" />
    <?php else: ?>
      <em>Using default logo (Images/logo.png)</em>
    <?php endif; ?>
  </div>

  <label for="logo">Replace Logo (optional)</label>
  <input id="logo" name="logo" type="file" accept="image/*" />

  <label for="map_embed_url">Map Embed URL</label>
  <input id="map_embed_url" name="map_embed_url" value="<?php echo htmlspecialchars($contact['map_embed_url'] ?? ''); ?>" />

  <button type="submit">Save</button>
</form>

<?php require __DIR__ . '/admin_footer.php'; ?>

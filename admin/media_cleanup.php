<?php require __DIR__ . '/admin_header.php'; ?>
<?php
// Media Cleanup: list images in Images/ not referenced by DB or code

$success = '';
$error = '';

// Ensure a CSRF token exists for POST actions (reuse admin pattern)
if (!isset($_SESSION['csrf_token'])) {
  try { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); } catch (Throwable $e) { $_SESSION['csrf_token'] = bin2hex((string)mt_rand()); }
}

$imagesDir = realpath(__DIR__ . '/../Images');
if ($imagesDir === false) { $error = 'Images directory not found.'; }

// Collect existing images
$allowedExt = ['jpg','jpeg','png','gif','webp','svg'];
$allImages = [];
if (!$error) {
  $dh = opendir($imagesDir);
  if ($dh) {
    while (($file = readdir($dh)) !== false) {
      if ($file === '.' || $file === '..') continue;
      $path = $imagesDir . DIRECTORY_SEPARATOR . $file;
      if (!is_file($path)) continue;
      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      if (!in_array($ext, $allowedExt, true)) continue;
      $allImages[$file] = $path;
    }
    closedir($dh);
  }
}

// Get references from DB
$referenced = [];
try {
  // shop images
  $stmt = $conn->query("SELECT image FROM shop WHERE image IS NOT NULL AND TRIM(image) <> ''");
  foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $img) { $referenced[trim($img)] = true; }
} catch (Throwable $e) { /* ignore */ }

try {
  // contact_info logo
  $r = $conn->query("SELECT logo FROM contact_info WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
  if ($r && !empty($r['logo'])) { $referenced[trim($r['logo'])] = true; }
} catch (Throwable $e) { /* ignore */ }

// Get references from codebase
$root = realpath(__DIR__ . '/..');
if ($root && !$error) {
  $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
  foreach ($rii as $fi) {
    if (!$fi->isFile()) continue;
    $ext = strtolower($fi->getExtension());
    if (!in_array($ext, ['php','html','css','js'], true)) continue;
    // Skip Images folder itself
    if (stripos($fi->getPathname(), DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR) !== false) continue;
    $content = @file_get_contents($fi->getPathname());
    if ($content === false) continue;
    foreach (array_keys($allImages) as $fname) {
      if (isset($referenced[$fname])) continue; // already marked
      if (strpos($content, $fname) !== false) { $referenced[$fname] = true; }
    }
  }
}

// Determine unused images
$unused = [];
foreach ($allImages as $fname => $path) {
  if (!isset($referenced[$fname])) { $unused[$fname] = $path; }
}

// Handle deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
  $token = $_POST['csrf_token'] ?? '';
  if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
    $error = 'Invalid request. Please refresh and try again.';
  } else {
    $toDelete = (array)($_POST['files'] ?? []);
    $deleted = 0; $failed = 0;
    foreach ($toDelete as $fname) {
      $fname = basename($fname);
      $path = $imagesDir . DIRECTORY_SEPARATOR . $fname;
      // Only allow delete if it is currently considered unused to avoid accidental deletion
      if (isset($unused[$fname]) && is_file($path)) {
        if (@unlink($path)) { $deleted++; unset($unused[$fname], $allImages[$fname]); }
        else { $failed++; }
      }
    }
    if ($deleted > 0 && $failed === 0) { $success = "$deleted image(s) deleted."; }
    elseif ($deleted > 0 && $failed > 0) { $success = "$deleted image(s) deleted, $failed failed."; }
    else { $error = 'No images deleted.'; }
  }
}
?>
<h2>Media Cleanup</h2>
<?php if (!empty($success)): ?><div class="register-message success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="register-message error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<p>This tool scans the Images folder and shows files not referenced by the database or code. Select and delete unused images to free space.</p>

<form method="post" onsubmit="return confirm('Delete selected images? This cannot be undone.');">
  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />

  <?php if (empty($unused)): ?>
    <p><em>No unused images found.</em></p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:30px;"><input type="checkbox" id="checkall" onclick="document.querySelectorAll('.chk').forEach(c=>c.checked=this.checked);" /></th>
          <th>Preview</th>
          <th>Filename</th>
          <th>Size</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($unused as $fname => $path): $sz = @filesize($path); ?>
          <tr>
            <td><input class="chk" type="checkbox" name="files[]" value="<?php echo htmlspecialchars($fname); ?>" /></td>
            <td><img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($fname); ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:6px;" /></td>
            <td><?php echo htmlspecialchars($fname); ?></td>
            <td><?php echo $sz !== false ? number_format($sz/1024, 1) . ' KB' : '-'; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <button type="submit" style="margin-top:10px;">Delete Selected</button>
  <?php endif; ?>
</form>

<?php require __DIR__ . '/admin_footer.php'; ?>

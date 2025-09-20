<?php require __DIR__ . '/admin_header.php'; ?>
<?php
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

$sql = "SELECT $pk AS id, name, type, color_theme, price, image FROM shop ORDER BY $pk DESC";
$rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Manage Flowers</h2>
<table class="admin-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Name</th>
      <th>Type</th>
      <th>Color</th>
      <th>Price</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?php echo (int)$r['id']; ?></td>
        <td><?php if (!empty($r['image'])): ?><img src="<?php echo APPURL; ?>Images/<?php echo htmlspecialchars($r['image']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:6px;" /><?php endif; ?></td>
        <td><?php echo htmlspecialchars($r['name']); ?></td>
        <td><?php echo htmlspecialchars($r['type']); ?></td>
        <td><?php echo htmlspecialchars($r['color_theme']); ?></td>
        <td>Rs. <?php echo number_format((float)$r['price'], 2); ?></td>
        <td>
          <a href="<?php echo APPURL; ?>admin/edit_flower.php?id=<?php echo (int)$r['id']; ?>">Edit</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/admin_footer.php'; ?>

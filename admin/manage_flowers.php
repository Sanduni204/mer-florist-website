<?php require __DIR__ . '/admin_header.php'; ?>
<?php
$rows = $conn->query('SELECT id, name, type, color_theme, price, image FROM shop ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
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
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__ . '/admin_footer.php'; ?>

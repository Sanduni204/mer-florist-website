<?php require __DIR__ . '/admin_header.php'; ?>
<style>
	.admin-profile { display:flex; align-items:center; gap:16px; margin-bottom:16px; }
	.admin-profile img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: none; }
	.admin-profile .info { display:flex; flex-direction:column; }
	.admin-profile .info .name { font-size: 1.2rem; font-weight: 600; }
	.admin-profile .info .role { color:#666; font-style: italic; }
	.admin-welcome { margin-top: 10px; }
		/* Narrower dashboard section on this page only */
		.admin-wrapper { max-width: 600px; }
	@media (max-width: 480px){ .admin-profile { flex-direction:column; align-items:flex-start; } }
</style>
<h2>Admin Dashboard</h2>
<div class="admin-profile">
	<img src="<?php echo APPURL; ?>Images/admin.png" alt="Admin" />
	<div class="info">
		<div class="name"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?></div>
		<div class="role">Administrator</div>
	</div>
  
</div>
<p class="admin-welcome">Welcome to the admin panel. Use the links above to manage flowers.</p>
<?php require __DIR__ . '/admin_footer.php'; ?>

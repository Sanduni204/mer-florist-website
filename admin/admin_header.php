<?php
session_start();
require_once __DIR__ . '/../Config/config.php';
if (!defined('APPURL')) {
  define('APPURL', 'http://localhost/mer_ecommerce/');
}
// Basic admin session check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ' . APPURL . 'admin/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Mer Florist</title>
  <link rel="stylesheet" type="text/css" href="<?php echo APPURL; ?>/1style.css?v=<?php echo time(); ?>">
  <style>
    .admin-wrapper { max-width: 1100px; margin: 100px auto 40px; padding: 0 20px; }
    .admin-nav { display:flex; gap: 12px; margin-bottom: 20px; }
    .admin-nav a { padding: 10px 14px; border:1px solid #ddd; border-radius:8px; background:#fff; }
    .admin-card { background:#fff; border:1px solid #eee; border-radius:12px; padding:20px; box-shadow:0 10px 20px rgba(0,0,0,0.05); }
    .admin-form label { display:block; margin:10px 0 6px; }
    .admin-form input, .admin-form select, .admin-form textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; }
    .admin-form button { margin-top:10px; padding:10px 16px; border:none; border-radius:8px; background:#222; color:#fff; cursor:pointer; }
    .admin-table { width:100%; border-collapse:collapse; }
    .admin-table th, .admin-table td { border-bottom:1px solid #eee; padding:10px; text-align:left; }
  </style>
</head>
<body>
  <div class="admin-wrapper">
    <div class="admin-nav">
      <a href="<?php echo APPURL; ?>admin/index.php">Dashboard</a>
      <a href="<?php echo APPURL; ?>admin/add_flower.php">Add Flower</a>
      <a href="<?php echo APPURL; ?>admin/manage_flowers.php">Manage Flowers</a>
      <a href="<?php echo APPURL; ?>admin/logout.php">Logout</a>
    </div>
    <div class="admin-card">

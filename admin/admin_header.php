<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../Config/config.php';
// Admin session check early, use absolute path to avoid APPURL redefinition issues
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: /mer_ecommerce/admin/login.php');
    exit;
}
// Include global site header (opens <html>, <head>, and <body> and renders main nav)
require_once __DIR__ . '/../includes/header.php';
?>
<!-- Admin local styles -->
<style>
  .admin-main { display:grid; place-items:center; }
  .admin-wrapper { max-width: 1100px; margin: 0 auto; padding: 0 20px; width: 100%; }
  .admin-nav { display:flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
  .admin-nav a { padding: 10px 14px; border:1px solid #ddd; border-radius:8px; background:#fff; }
  .admin-card { background:#fff; border:1px solid #eee; border-radius:12px; padding:20px; box-shadow:0 10px 20px rgba(0,0,0,0.05); }
  .admin-form label { display:block; margin:10px 0 6px; }
  .admin-form input, .admin-form select, .admin-form textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; }
  .admin-form button { margin-top:10px; padding:10px 16px; border:none; border-radius:8px; background:#222; color:#fff; cursor:pointer; }
  .admin-table { width:100%; border-collapse:collapse; }
  .admin-table th, .admin-table td { border-bottom:1px solid #eee; padding:10px; text-align:left; }
</style>

<div class="main-content admin-main">
  <div class="admin-wrapper">
    <div class="admin-card">

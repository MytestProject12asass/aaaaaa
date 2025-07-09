<?php
/**
 * StarRent.vip - Admin Logout
 */

session_start();

// Clear admin session
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);

// Redirect to admin login
header('Location: /admin/login.php');
exit;
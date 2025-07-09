<?php
/**
 * StarRent.vip - User Logout
 */

session_start();

// Clear user session
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

// Redirect to homepage
header('Location: /index.php');
exit;
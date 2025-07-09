<?php
/**
 * Application Configuration
 * StarRent.vip - Starlink Router Rental Platform
 */

// Suppress ImageMagick version warnings
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('UTC');

// Application settings
define('APP_NAME', 'StarRent.vip');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://star-rent.vip'); // Update with your domain

// File upload settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Security
define('HASH_ALGO', PASSWORD_DEFAULT);
define('CSRF_TOKEN_LENGTH', 32);

// Email settings (update with your SMTP details)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@star-rent.vip');
define('SMTP_FROM_NAME', 'StarRent.vip');

// Plisio API settings (get from https://plisio.net/)
define('PLISIO_API_KEY', 'your_plisio_api_key');
define('PLISIO_SECRET_KEY', 'your_plisio_secret_key');
define('PLISIO_WEBHOOK_URL', APP_URL . '/webhook/plisio.php');

// Currency settings
define('DEFAULT_CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Rental settings
define('MIN_RENTAL_DAYS', 1);
define('MAX_RENTAL_DAYS', 365);
define('LATE_FEE_PERCENTAGE', 5.0);
define('DAMAGE_ASSESSMENT_FEE', 50.00);

// Load database configuration
require_once __DIR__ . '/database.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Image processing configuration
if (extension_loaded('imagick')) {
    // Suppress ImageMagick version warnings
    $imagick = new Imagick();
    $imagick->setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 256);
    $imagick->setResourceLimit(Imagick::RESOURCETYPE_MAP, 256);
}

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function flash($key, $message = null) {
    if ($message === null) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    $_SESSION['flash'][$key] = $message;
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function format_currency($amount, $currency = DEFAULT_CURRENCY) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function generate_unique_id($prefix = '') {
    return $prefix . uniqid() . mt_rand(1000, 9999);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        redirect('/admin/login.php');
    }
}
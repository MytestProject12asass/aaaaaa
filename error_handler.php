<?php
/**
 * Global Error Handler
 * StarRent.vip - Starlink Router Rental Platform
 */

// Suppress ImageMagick version warnings globally
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Ignore ImageMagick version warnings
    if (strpos($errstr, 'Imagick was compiled against ImageMagick') !== false) {
        return true;
    }
    
    // Ignore other common warnings
    $ignorePatterns = [
        'Version warning',
        'ImageMagick version',
        'Imagick will run but may behave surprisingly'
    ];
    
    foreach ($ignorePatterns as $pattern) {
        if (strpos($errstr, $pattern) !== false) {
            return true;
        }
    }
    
    // Log other errors
    $errorTypes = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    ];
    
    $errorType = $errorTypes[$errno] ?? 'UNKNOWN';
    $logMessage = "[" . date('Y-m-d H:i:s') . "] {$errorType}: {$errstr} in {$errfile} on line {$errline}\n";
    
    error_log($logMessage, 3, __DIR__ . '/logs/error.log');
    
    // Don't execute PHP internal error handler
    return true;
}

// Set custom error handler
set_error_handler('customErrorHandler');

// Custom exception handler
function customExceptionHandler($exception) {
    $logMessage = "[" . date('Y-m-d H:i:s') . "] EXCEPTION: " . $exception->getMessage() . 
                  " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    
    error_log($logMessage, 3, __DIR__ . '/logs/error.log');
    
    // Show user-friendly error page in production
    if (!defined('DEBUG_MODE') || !DEBUG_MODE) {
        http_response_code(500);
        include 'error_pages/500.html';
        exit;
    }
}

// Set custom exception handler
set_exception_handler('customExceptionHandler');

// Shutdown function to catch fatal errors
function shutdownHandler() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        $logMessage = "[" . date('Y-m-d H:i:s') . "] FATAL: " . $error['message'] . 
                      " in " . $error['file'] . " on line " . $error['line'] . "\n";
        
        error_log($logMessage, 3, __DIR__ . '/logs/error.log');
    }
}

register_shutdown_function('shutdownHandler');
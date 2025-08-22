<?php
// Configuration loader
// Load environment configuration

// Check if .env.php exists, otherwise use defaults
if (file_exists(__DIR__ . '/../.env.php')) {
    require_once __DIR__ . '/../.env.php';
} else {
    // Default configuration for development
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'notes_app');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('CONTENT_MAX_CHARS', 10000);
    define('TIMEZONE', 'UTC');
    define('BASE_URL', 'http://localhost/notes/');
    define('CSRF_TOKEN_SECRET', 'default-secret-key-change-in-production');
    define('DEBUG_MODE', true);
    define('ERROR_REPORTING', E_ALL);
}

// Set error reporting
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(ERROR_REPORTING);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
if (defined('TIMEZONE')) {
    date_default_timezone_set(TIMEZONE);
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Start session for CSRF protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

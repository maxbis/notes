<?php
// Example configuration file - copy to .env.php and customize

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'notes_app');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('CONTENT_MAX_CHARS', 10000);
define('TIMEZONE', 'UTC');
define('BASE_URL', 'http://localhost/notes/');

// Security (optional)
define('CSRF_TOKEN_SECRET', 'your-secret-key-here');

// Development settings
define('DEBUG_MODE', true);
define('ERROR_REPORTING', E_ALL);

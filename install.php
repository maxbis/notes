<?php
// Notes App Installation Script
// Run this script to set up your database and configuration

$pageTitle = 'Installation';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Notes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📝 Notes App Installation</h1>
            <p class="text-gray-600">Set up your note-taking application</p>
        </div>

        <?php
        $step = $_GET['step'] ?? 1;
        $error = '';
        $success = '';

        // Check PHP version
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            $error = 'PHP 8.1 or higher is required. Current version: ' . PHP_VERSION;
        }

        // Check PDO extension
        if (!extension_loaded('pdo_mysql')) {
            $error = 'PDO MySQL extension is required but not installed.';
        }

        // Check if .env.php exists
        $envExists = file_exists('.env.php');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
            // Handle database configuration
            $dbHost = $_POST['db_host'] ?? '';
            $dbName = $_POST['db_name'] ?? '';
            $dbUser = $_POST['db_user'] ?? '';
            $dbPass = $_POST['db_pass'] ?? '';
            
            if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
                $error = 'Please fill in all required database fields.';
            } else {
                try {
                    // Test database connection
                    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbUser, $dbPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    
                    // Create .env.php file
                    $envContent = "<?php
// Database Configuration
define('DB_HOST', '" . addslashes($dbHost) . "');
define('DB_NAME', '" . addslashes($dbName) . "');
define('DB_USER', '" . addslashes($dbUser) . "');
define('DB_PASS', '" . addslashes($dbPass) . "');

// Application Configuration
define('CONTENT_MAX_CHARS', 10000);
define('TIMEZONE', 'UTC');
define('BASE_URL', '" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/');

// Security
define('CSRF_TOKEN_SECRET', '" . bin2hex(random_bytes(32)) . "');

// Development settings
define('DEBUG_MODE', false);
define('ERROR_REPORTING', 0);
";
                    
                    if (file_put_contents('.env.php', $envContent)) {
                        $success = 'Configuration file created successfully!';
                        $step = 3;
                    } else {
                        $error = 'Failed to create configuration file. Check file permissions.';
                    }
                    
                } catch (PDOException $e) {
                    $error = 'Database connection failed: ' . $e->getMessage();
                }
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 3) {
            // Handle database setup
            try {
                require_once '.env.php';
                require_once 'includes/database.php';
                
                $db = Database::getInstance();
                
                // Import schema
                $schemaFile = 'database/schema.sql';
                if (file_exists($schemaFile)) {
                    $schema = file_get_contents($schemaFile);
                    $pdo = $db->getConnection();
                    
                    // Execute schema
                    $pdo->exec($schema);
                    $success = 'Database setup completed successfully!';
                    $step = 4;
                } else {
                    $error = 'Schema file not found.';
                }
                
            } catch (Exception $e) {
                $error = 'Database setup failed: ' . $e->getMessage();
            }
        }
        ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Step 1: Requirements Check -->
        <?php if ($step == 1): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 1: System Requirements</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">PHP Version (8.1+)</span>
                        <span class="<?php echo version_compare(PHP_VERSION, '8.1.0', '>=') ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                            <?php echo PHP_VERSION; ?>
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">PDO MySQL Extension</span>
                        <span class="<?php echo extension_loaded('pdo_mysql') ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                            <?php echo extension_loaded('pdo_mysql') ? 'Installed' : 'Not Installed'; ?>
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-700">Configuration File</span>
                        <span class="<?php echo $envExists ? 'text-green-600' : 'text-yellow-600'; ?> font-medium">
                            <?php echo $envExists ? 'Exists' : 'Not Found'; ?>
                        </span>
                    </div>
                </div>
                
                <?php if (empty($error)): ?>
                    <div class="mt-6">
                        <a href="?step=2" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Continue to Database Setup
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800 text-sm">
                            Please resolve the requirements issues before continuing.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Step 2: Database Configuration -->
        <?php if ($step == 2): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 2: Database Configuration</h2>
                
                <form method="POST" action="?step=2" class="space-y-4">
                    <div>
                        <label for="db_host" class="block text-sm font-medium text-gray-700 mb-2">Database Host</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="db_name" class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                        <input type="text" id="db_name" name="db_name" value="notes_app" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="db_user" class="block text-sm font-medium text-gray-700 mb-2">Database Username</label>
                        <input type="text" id="db_user" name="db_user" value="root" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="db_pass" class="block text-sm font-medium text-gray-700 mb-2">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Leave empty if no password is set</p>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Test Connection & Create Config
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Step 3: Database Setup -->
        <?php if ($step == 3): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Step 3: Database Setup</h2>
                
                <p class="text-gray-600 mb-4">Ready to create the database tables and sample data.</p>
                
                <form method="POST" action="?step=3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Create Database Tables
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Step 4: Installation Complete -->
        <?php if ($step == 4): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="mx-auto h-24 w-24 text-green-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Installation Complete!</h2>
                
                <p class="text-gray-600 mb-6">
                    Your Notes App has been successfully installed and configured.
                </p>
                
                <div class="space-y-3">
                    <a href="index.php" class="block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        Go to Notes App
                    </a>
                    
                    <a href="install.php" class="block bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                        Run Installation Again
                    </a>
                </div>
                
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg text-left">
                    <h3 class="font-medium text-blue-900 mb-2">Next Steps:</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Delete or rename this install.php file for security</li>
                        <li>• Create your first note using the "New Note" button</li>
                        <li>• Use #tags in your notes for organization</li>
                        <li>• Check the README.md for more information</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Navigation -->
        <div class="mt-8 text-center">
            <div class="inline-flex items-center space-x-2 bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-2">
                <span class="text-sm text-gray-600">Step <?php echo $step; ?> of 4</span>
                <div class="flex space-x-1">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="w-2 h-2 rounded-full <?php echo $i <= $step ? 'bg-blue-600' : 'bg-gray-300'; ?>"></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

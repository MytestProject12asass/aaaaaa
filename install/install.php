<?php
/**
 * StarRent.vip Installation Script
 * Starlink Router Rental Platform
 */

// Check if already installed
if (file_exists('../config/installed.lock')) {
    die('Application is already installed. Delete config/installed.lock to reinstall.');
}

$step = $_GET['step'] ?? 1;
$errors = [];
$success = [];

// Handle form submissions
if ($_POST) {
    switch ($step) {
        case 2:
            // Database configuration
            $dbHost = $_POST['db_host'] ?? '';
            $dbName = $_POST['db_name'] ?? '';
            $dbUser = $_POST['db_user'] ?? '';
            $dbPass = $_POST['db_pass'] ?? '';
            
            // Test database connection
            try {
                $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Save database config
                $configContent = file_get_contents('../config/database.php');
                $configContent = str_replace('your_db_username', $dbUser, $configContent);
                $configContent = str_replace('your_db_password', $dbPass, $configContent);
                $configContent = str_replace('your_db_name', $dbName, $configContent);
                $configContent = str_replace('localhost', $dbHost, $configContent);
                
                file_put_contents('../config/database.php', $configContent);
                
                $success[] = 'Database connection successful!';
                $step = 3;
            } catch (PDOException $e) {
                $errors[] = 'Database connection failed: ' . $e->getMessage();
            }
            break;
            
        case 3:
            // Import database
            try {
                require_once '../config/database.php';
                $db = Database::getInstance();
                
                $sql = file_get_contents('../database/starlink_rental.sql');
                $db->getConnection()->exec($sql);
                
                $success[] = 'Database imported successfully!';
                $step = 4;
            } catch (Exception $e) {
                $errors[] = 'Database import failed: ' . $e->getMessage();
            }
            break;
            
        case 4:
            // Site configuration
            $siteName = $_POST['site_name'] ?? 'StarRent.vip';
            $siteUrl = $_POST['site_url'] ?? '';
            $adminEmail = $_POST['admin_email'] ?? '';
            $adminPassword = $_POST['admin_password'] ?? '';
            $plisioApiKey = $_POST['plisio_api_key'] ?? '';
            $plisioSecretKey = $_POST['plisio_secret_key'] ?? '';
            
            // Update config file
            $configContent = file_get_contents('../config/config.php');
            $configContent = str_replace('https://star-rent.vip', $siteUrl, $configContent);
            $configContent = str_replace('your_plisio_api_key', $plisioApiKey, $configContent);
            $configContent = str_replace('your_plisio_secret_key', $plisioSecretKey, $configContent);
            
            file_put_contents('../config/config.php', $configContent);
            
            // Update admin user
            try {
                require_once '../config/database.php';
                $db = Database::getInstance();
                
                $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
                $db->update('admins', [
                    'email' => $adminEmail,
                    'password' => $hashedPassword,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = 1');
                
                // Update settings
                $db->update('settings', [
                    'site_name' => $siteName,
                    'contact_email' => $adminEmail,
                    'plisio_api_key' => $plisioApiKey,
                    'plisio_secret_key' => $plisioSecretKey,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = 1');
                
                $success[] = 'Site configuration saved successfully!';
                $step = 5;
            } catch (Exception $e) {
                $errors[] = 'Configuration failed: ' . $e->getMessage();
            }
            break;
            
        case 5:
            // Finalize installation
            file_put_contents('../config/installed.lock', date('Y-m-d H:i:s'));
            
            // Create uploads directory
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0755, true);
                mkdir('../uploads/routers', 0755, true);
                mkdir('../uploads/users', 0755, true);
                mkdir('../uploads/tickets', 0755, true);
            }
            
            $success[] = 'Installation completed successfully!';
            $step = 6;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarRent.vip Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">StarRent.vip Installation</h2>
                <p class="mt-2 text-sm text-gray-600">Step <?php echo $step; ?> of 6</p>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo ($step / 6) * 100; ?>%"></div>
            </div>
            
            <!-- Error Messages -->
            <?php if ($errors): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Success Messages -->
            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php foreach ($success as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="bg-white shadow-md rounded-lg p-6">
                <?php if ($step == 1): ?>
                <!-- Welcome -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Welcome to StarRent.vip</h3>
                <p class="text-gray-600 mb-6">This installer will help you set up your Starlink router rental platform. Please ensure you have:</p>
                <ul class="list-disc list-inside text-gray-600 mb-6 space-y-1">
                    <li>MySQL database credentials</li>
                    <li>Plisio.net API keys (optional)</li>
                    <li>SMTP email settings (optional)</li>
                </ul>
                <a href="?step=2" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 inline-block text-center">Start Installation</a>
                
                <?php elseif ($step == 2): ?>
                <!-- Database Configuration -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Database Configuration</h3>
                <form method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Database Host</label>
                            <input type="text" name="db_host" value="localhost" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Database Name</label>
                            <input type="text" name="db_name" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Database Username</label>
                            <input type="text" name="db_user" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Database Password</label>
                            <input type="password" name="db_pass" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-6 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Test Connection</button>
                </form>
                
                <?php elseif ($step == 3): ?>
                <!-- Import Database -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Import Database</h3>
                <p class="text-gray-600 mb-6">Click the button below to import the database structure and sample data.</p>
                <form method="POST">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Import Database</button>
                </form>
                
                <?php elseif ($step == 4): ?>
                <!-- Site Configuration -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Site Configuration</h3>
                <form method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Site Name</label>
                            <input type="text" name="site_name" value="StarRent.vip" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Site URL</label>
                            <input type="url" name="site_url" placeholder="https://your-domain.com" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Email</label>
                            <input type="email" name="admin_email" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Password</label>
                            <input type="password" name="admin_password" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plisio API Key (Optional)</label>
                            <input type="text" name="plisio_api_key" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plisio Secret Key (Optional)</label>
                            <input type="text" name="plisio_secret_key" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-6 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Save Configuration</button>
                </form>
                
                <?php elseif ($step == 5): ?>
                <!-- Finalize -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Finalize Installation</h3>
                <p class="text-gray-600 mb-6">Click the button below to complete the installation process.</p>
                <form method="POST">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">Complete Installation</button>
                </form>
                
                <?php elseif ($step == 6): ?>
                <!-- Complete -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Installation Complete!</h3>
                <p class="text-gray-600 mb-6">Your StarRent.vip platform has been successfully installed. You can now:</p>
                <div class="space-y-3">
                    <a href="../index.php" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 inline-block text-center">Visit Your Site</a>
                    <a href="../admin/login.php" class="w-full bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700 inline-block text-center">Admin Login</a>
                </div>
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800"><strong>Important:</strong> Please delete the /install directory for security reasons.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
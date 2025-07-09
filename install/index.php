<?php
/**
 * StarRent.vip Installation Wizard
 * Easy cPanel Installation System
 */

session_start();

// Check if already installed
if (file_exists('../config/installed.lock')) {
    die('<div style="text-align:center;margin-top:50px;font-family:Arial,sans-serif;"><h2>✅ Installation Complete!</h2><p>StarRent.vip is already installed.</p><a href="../index.php" style="color:#2563eb;">Visit Your Site</a></div>');
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];
$success = [];

// Handle form submissions
if ($_POST) {
    switch ($step) {
        case 2:
            // Database Test
            $host = $_POST['db_host'] ?? 'localhost';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';
            
            if (empty($name) || empty($user)) {
                $errors[] = 'Database name and username are required';
            } else {
                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Store database config in session
                    $_SESSION['db_config'] = [
                        'host' => $host,
                        'name' => $name,
                        'user' => $user,
                        'pass' => $pass
                    ];
                    
                    $success[] = 'Database connection successful!';
                    $step = 3;
                } catch (PDOException $e) {
                    $errors[] = 'Database connection failed: ' . $e->getMessage();
                }
            }
            break;
            
        case 3:
            // Import Database
            if (!isset($_SESSION['db_config'])) {
                $errors[] = 'Database configuration not found. Please go back to step 2.';
                $step = 2;
            } else {
                try {
                    $config = $_SESSION['db_config'];
                    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4", 
                                   $config['user'], $config['pass']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Read SQL file
                    $sqlFile = '../database/starlink_rental.sql';
                    if (!file_exists($sqlFile)) {
                        $errors[] = 'Database file not found: ' . $sqlFile;
                    } else {
                        $sql = file_get_contents($sqlFile);
                        if ($sql) {
                            // Remove comments and split into statements
                            $sql = preg_replace('/--.*$/m', '', $sql);
                            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                            
                            // Split by semicolon but ignore semicolons in quotes
                            $statements = [];
                            $current = '';
                            $inQuotes = false;
                            $quoteChar = '';
                            
                            for ($i = 0; $i < strlen($sql); $i++) {
                                $char = $sql[$i];
                                
                                if (!$inQuotes && ($char === '"' || $char === "'")) {
                                    $inQuotes = true;
                                    $quoteChar = $char;
                                } elseif ($inQuotes && $char === $quoteChar) {
                                    $inQuotes = false;
                                } elseif (!$inQuotes && $char === ';') {
                                    $statement = trim($current);
                                    if (!empty($statement)) {
                                        $statements[] = $statement;
                                    }
                                    $current = '';
                                    continue;
                                }
                                
                                $current .= $char;
                            }
                            
                            // Add the last statement if it exists
                            $statement = trim($current);
                            if (!empty($statement)) {
                                $statements[] = $statement;
                            }
                            
                            // Execute each statement
                            foreach ($statements as $statement) {
                                $statement = trim($statement);
                                if (!empty($statement) && 
                                    !preg_match('/^(SET|START|COMMIT)/i', $statement)) {
                                    try {
                                        $pdo->exec($statement);
                                    } catch (PDOException $e) {
                                        // Log the error but continue with other statements
                                        error_log("SQL Error: " . $e->getMessage() . " in statement: " . substr($statement, 0, 100));
                                    }
                                }
                            }
                            
                            $success[] = 'Database imported successfully!';
                            $step = 4;
                        } else {
                            $errors[] = 'Could not read database file';
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = 'Database import failed: ' . $e->getMessage();
                }
            }
            break;
            
        case 4:
            // Site Configuration
            $site_name = $_POST['site_name'] ?? 'StarRent.vip';
            $site_url = $_POST['site_url'] ?? '';
            $admin_email = $_POST['admin_email'] ?? '';
            $admin_password = $_POST['admin_password'] ?? '';
            $plisio_api = $_POST['plisio_api'] ?? '';
            $plisio_secret = $_POST['plisio_secret'] ?? '';
            
            if (empty($site_url) || empty($admin_email) || empty($admin_password)) {
                $errors[] = 'Site URL, admin email, and password are required';
            } elseif (strlen($admin_password) < 8) {
                $errors[] = 'Admin password must be at least 8 characters long';
            } else {
                try {
                    // Update database configuration file
                    $config = $_SESSION['db_config'];
                    $db_config = file_get_contents('../config/database.php');
                    $db_config = str_replace('your_db_username', $config['user'], $db_config);
                    $db_config = str_replace('your_db_password', $config['pass'], $db_config);
                    $db_config = str_replace('your_db_name', $config['name'], $db_config);
                    $db_config = str_replace('localhost', $config['host'], $db_config);
                    file_put_contents('../config/database.php', $db_config);
                    
                    // Update main configuration
                    $main_config = file_get_contents('../config/config.php');
                    $main_config = str_replace('https://star-rent.vip', rtrim($site_url, '/'), $main_config);
                    $main_config = str_replace('your_plisio_api_key', $plisio_api, $main_config);
                    $main_config = str_replace('your_plisio_secret_key', $plisio_secret, $main_config);
                    file_put_contents('../config/config.php', $main_config);
                    
                    // Update admin user in database
                    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4", 
                                   $config['user'], $config['pass']);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admins SET email = ?, password = ?, updated_at = NOW() WHERE id = 1");
                    $stmt->execute([$admin_email, $hashed_password]);
                    
                    // Update site settings
                    $stmt = $pdo->prepare("UPDATE settings SET site_name = ?, contact_email = ?, plisio_api_key = ?, plisio_secret_key = ?, updated_at = NOW() WHERE id = 1");
                    $stmt->execute([$site_name, $admin_email, $plisio_api, $plisio_secret]);
                    
                    $_SESSION['install_config'] = [
                        'site_name' => $site_name,
                        'site_url' => $site_url,
                        'admin_email' => $admin_email
                    ];
                    
                    $success[] = 'Configuration saved successfully!';
                    $step = 5;
                } catch (Exception $e) {
                    $errors[] = 'Configuration failed: ' . $e->getMessage();
                }
            }
            break;
            
        case 5:
            // Finalize Installation
            try {
                // Create uploads directories
                $upload_dirs = [
                    '../uploads',
                    '../uploads/routers',
                    '../uploads/users',
                    '../uploads/tickets',
                    '../assets/images/routers'
                ];
                
                foreach ($upload_dirs as $dir) {
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                }
                
                // Create sample router images (placeholder)
                $sample_images = [
                    'starlink-standard.jpg',
                    'starlink-business.jpg',
                    'starlink-rv.jpg',
                    'starlink-maritime.jpg',
                    'starlink-aviation.jpg'
                ];
                
                foreach ($sample_images as $image) {
                    $image_path = '../assets/images/routers/' . $image;
                    if (!file_exists($image_path)) {
                        // Create a simple placeholder image URL
                        file_put_contents($image_path, '');
                    }
                }
                
                // Create installation lock file
                file_put_contents('../config/installed.lock', date('Y-m-d H:i:s'));
                
                // Clear session
                unset($_SESSION['db_config']);
                unset($_SESSION['install_config']);
                
                $success[] = 'Installation completed successfully!';
                $step = 6;
            } catch (Exception $e) {
                $errors[] = 'Finalization failed: ' . $e->getMessage();
            }
            break;
    }
}

// System requirements check
function checkRequirements() {
    return [
        'PHP Version ≥ 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'MySQL Extension' => extension_loaded('pdo_mysql'),
        'cURL Extension' => extension_loaded('curl'),
        'JSON Extension' => extension_loaded('json'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'GD Extension' => extension_loaded('gd'),
        'Config Directory Writable' => is_writable('../config/'),
        'Uploads Directory Writable' => is_writable('../') || mkdir('../uploads', 0755, true),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarRent.vip Installation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 700px; margin: 0 auto; }
        .card { 
            background: white; 
            border-radius: 16px; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
            padding: 40px; 
            margin-bottom: 20px;
        }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { 
            font-size: 32px; 
            font-weight: bold; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px; 
        }
        .subtitle { color: #6b7280; font-size: 16px; }
        .progress { 
            background: #e5e7eb; 
            height: 8px; 
            border-radius: 4px; 
            margin: 30px 0; 
            overflow: hidden; 
        }
        .progress-bar { 
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); 
            height: 100%; 
            transition: width 0.3s ease; 
            border-radius: 4px;
        }
        .step-indicator { 
            display: flex; 
            justify-content: center; 
            margin-bottom: 30px; 
            flex-wrap: wrap;
            gap: 10px;
        }
        .step { 
            width: 45px; 
            height: 45px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold; 
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .step.active { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            transform: scale(1.1);
        }
        .step.completed { 
            background: #10b981; 
            color: white; 
        }
        .step.pending { 
            background: #e5e7eb; 
            color: #6b7280; 
        }
        .form-group { margin-bottom: 25px; }
        .form-label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #374151; 
            font-size: 14px;
        }
        .form-input { 
            width: 100%; 
            padding: 14px 16px; 
            border: 2px solid #e5e7eb; 
            border-radius: 10px; 
            font-size: 16px; 
            transition: all 0.3s ease;
        }
        .form-input:focus { 
            outline: none; 
            border-color: #667eea; 
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-help { 
            font-size: 12px; 
            color: #6b7280; 
            margin-top: 5px; 
        }
        .btn { 
            display: inline-block; 
            padding: 14px 28px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            text-decoration: none; 
            border-radius: 10px; 
            border: none; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600; 
            transition: all 0.3s ease;
        }
        .btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary { 
            background: #6b7280; 
        }
        .btn-secondary:hover { 
            background: #4b5563; 
        }
        .alert { 
            padding: 16px 20px; 
            border-radius: 10px; 
            margin-bottom: 25px; 
            font-weight: 500;
        }
        .alert-success { 
            background: #d1fae5; 
            color: #065f46; 
            border: 1px solid #a7f3d0; 
        }
        .alert-error { 
            background: #fee2e2; 
            color: #991b1b; 
            border: 1px solid #fca5a5; 
        }
        .requirements { 
            background: #f8fafc; 
            padding: 25px; 
            border-radius: 12px; 
            margin-bottom: 25px; 
            border: 1px solid #e2e8f0;
        }
        .requirement { 
            display: flex; 
            align-items: center; 
            margin-bottom: 12px; 
            font-size: 14px;
        }
        .requirement.ok { color: #059669; }
        .requirement.error { color: #dc2626; }
        .requirement-icon { 
            margin-right: 12px; 
            font-weight: bold; 
            font-size: 16px;
        }
        .feature-list { 
            list-style: none; 
            margin: 20px 0; 
        }
        .feature-list li { 
            padding: 8px 0; 
            padding-left: 25px; 
            position: relative; 
        }
        .feature-list li:before { 
            content: "✓"; 
            position: absolute; 
            left: 0; 
            color: #10b981; 
            font-weight: bold; 
        }
        .success-actions { 
            display: flex; 
            gap: 15px; 
            flex-wrap: wrap; 
            justify-content: center;
        }
        .security-notice { 
            margin-top: 25px; 
            padding: 20px; 
            background: #fef3c7; 
            border-radius: 10px; 
            color: #92400e; 
            border: 1px solid #fbbf24;
        }
        .security-notice strong { color: #78350f; }
        
        @media (max-width: 640px) {
            .card { padding: 25px; }
            .step { width: 35px; height: 35px; font-size: 14px; }
            .success-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">⚡ StarRent.vip</div>
                <p class="subtitle">Starlink Router Rental Platform Installation</p>
            </div>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="step <?php echo $i < $step ? 'completed' : ($i == $step ? 'active' : 'pending'); ?>">
                    <?php echo $i; ?>
                </div>
                <?php endfor; ?>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress">
                <div class="progress-bar" style="width: <?php echo ($step / 6) * 100; ?>%"></div>
            </div>
            
            <!-- Error Messages -->
            <?php if ($errors): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Success Messages -->
            <?php if ($success): ?>
            <div class="alert alert-success">
                <?php foreach ($success as $message): ?>
                <div><?php echo htmlspecialchars($message); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($step == 1): ?>
            <!-- Step 1: Welcome & Requirements -->
            <h2 style="margin-bottom: 20px; color: #1f2937;">Welcome to StarRent.vip</h2>
            <p style="margin-bottom: 25px; color: #4b5563; line-height: 1.6;">
                This installer will set up your professional Starlink router rental platform with cryptocurrency payment support via Plisio.net.
            </p>
            
            <div class="requirements">
                <h3 style="margin-bottom: 20px; color: #374151;">System Requirements Check</h3>
                <?php
                $requirements = checkRequirements();
                $allPassed = true;
                
                foreach ($requirements as $req => $status):
                    if (!$status) $allPassed = false;
                ?>
                <div class="requirement <?php echo $status ? 'ok' : 'error'; ?>">
                    <span class="requirement-icon"><?php echo $status ? '✓' : '✗'; ?></span>
                    <?php echo $req; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <h3 style="margin-bottom: 15px; color: #374151;">What You'll Get:</h3>
            <ul class="feature-list">
                <li>Complete Starlink router rental management system</li>
                <li>Cryptocurrency payment processing via Plisio.net</li>
                <li>User registration and authentication system</li>
                <li>Admin dashboard for managing rentals and inventory</li>
                <li>Responsive design that works on all devices</li>
                <li>Professional booking and payment workflow</li>
            </ul>
            
            <h3 style="margin-bottom: 15px; color: #374151;">What You'll Need:</h3>
            <ul style="margin: 15px 0 30px 20px; color: #4b5563;">
                <li>MySQL database credentials from cPanel</li>
                <li>Your domain name (with SSL certificate)</li>
                <li>Admin email and secure password</li>
                <li>Plisio.net API keys (optional - for crypto payments)</li>
            </ul>
            
            <?php if ($allPassed): ?>
            <a href="?step=2" class="btn">🚀 Start Installation</a>
            <?php else: ?>
            <div class="alert alert-error">
                Please fix the system requirements above before proceeding with installation.
            </div>
            <?php endif; ?>
            
            <?php elseif ($step == 2): ?>
            <!-- Step 2: Database Configuration -->
            <h2 style="margin-bottom: 20px; color: #1f2937;">Database Configuration</h2>
            <p style="margin-bottom: 25px; color: #4b5563;">
                Enter your MySQL database details from cPanel. Make sure you've created the database and user with full privileges.
            </p>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" value="localhost" class="form-input" required>
                    <div class="form-help">Usually 'localhost' for cPanel hosting</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-input" placeholder="username_starrent" required>
                    <div class="form-help">The database name you created in cPanel</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Database Username</label>
                    <input type="text" name="db_user" class="form-input" required>
                    <div class="form-help">Database user with full privileges</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Database Password</label>
                    <input type="password" name="db_pass" class="form-input">
                    <div class="form-help">Leave empty if no password is set</div>
                </div>
                
                <button type="submit" class="btn">🔍 Test Database Connection</button>
            </form>
            
            <?php elseif ($step == 3): ?>
            <!-- Step 3: Import Database -->
            <h2 style="margin-bottom: 20px; color: #1f2937;">Import Database Structure</h2>
            <p style="margin-bottom: 25px; color: #4b5563;">
                Click below to import the database tables and sample data. This will create all necessary tables for your Starlink rental platform.
            </p>
            
            <div style="background: #f0f9ff; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #0ea5e9;">
                <h4 style="color: #0c4a6e; margin-bottom: 10px;">📊 What will be imported:</h4>
                <ul style="color: #0c4a6e; margin-left: 20px;">
                    <li>User management tables</li>
                    <li>Router inventory system</li>
                    <li>Rental booking system</li>
                    <li>Payment processing tables</li>
                    <li>Support ticket system</li>
                    <li>Sample router data</li>
                </ul>
            </div>
            
            <form method="POST">
                <button type="submit" class="btn">📥 Import Database</button>
            </form>
            
            <?php elseif ($step == 4): ?>
            <!-- Step 4: Site Configuration -->
            <h2 style="margin-bottom: 20px; color: #1f2937;">Site Configuration</h2>
            <p style="margin-bottom: 25px; color: #4b5563;">
                Configure your site settings and create the admin account. Make sure to use a strong password for security.
            </p>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" value="StarRent.vip" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Site URL</label>
                    <input type="url" name="site_url" placeholder="https://yourdomain.com" class="form-input" required>
                    <div class="form-help">Your full domain URL (with https:// - SSL required for payments)</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Admin Email</label>
                    <input type="email" name="admin_email" class="form-input" required>
                    <div class="form-help">This will be your admin login email</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Admin Password</label>
                    <input type="password" name="admin_password" class="form-input" required>
                    <div class="form-help">Minimum 8 characters - use a strong password</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Plisio API Key (Optional)</label>
                    <input type="text" name="plisio_api" class="form-input" placeholder="Get from plisio.net">
                    <div class="form-help">Required for cryptocurrency payments - get from <a href="https://plisio.net" target="_blank" style="color: #2563eb;">plisio.net</a></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Plisio Secret Key (Optional)</label>
                    <input type="text" name="plisio_secret" class="form-input" placeholder="Get from plisio.net">
                    <div class="form-help">Secret key for webhook verification</div>
                </div>
                
                <button type="submit" class="btn">💾 Save Configuration</button>
            </form>
            
            <?php elseif ($step == 5): ?>
            <!-- Step 5: Finalize -->
            <h2 style="margin-bottom: 20px; color: #1f2937;">Finalize Installation</h2>
            <p style="margin-bottom: 25px; color: #4b5563;">
                Complete the installation process by creating necessary directories and finalizing the setup.
            </p>
            
            <div style="background: #f0fdf4; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #22c55e;">
                <h4 style="color: #15803d; margin-bottom: 10px;">🎯 Final steps:</h4>
                <ul style="color: #15803d; margin-left: 20px;">
                    <li>Create upload directories</li>
                    <li>Set proper file permissions</li>
                    <li>Generate installation lock file</li>
                    <li>Prepare sample router images</li>
                </ul>
            </div>
            
            <form method="POST">
                <button type="submit" class="btn">✅ Complete Installation</button>
            </form>
            
            <?php elseif ($step == 6): ?>
            <!-- Step 6: Complete -->
            <div style="text-align: center;">
                <div style="font-size: 64px; margin-bottom: 20px;">🎉</div>
                <h2 style="margin-bottom: 20px; color: #1f2937;">Installation Complete!</h2>
                <p style="margin-bottom: 30px; color: #4b5563; font-size: 18px;">
                    Your StarRent.vip platform has been successfully installed and is ready to use!
                </p>
            </div>
            
            <div style="background: #f0f9ff; padding: 25px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #0ea5e9;">
                <h3 style="color: #0c4a6e; margin-bottom: 15px;">🚀 Next Steps:</h3>
                <ol style="color: #0c4a6e; margin-left: 20px; line-height: 1.8;">
                    <li><strong>Delete the /install directory</strong> for security (IMPORTANT!)</li>
                    <li>Set up SSL certificate (required for cryptocurrency payments)</li>
                    <li>Configure SMTP email settings in admin panel</li>
                    <li>Add your router inventory with real images</li>
                    <li>Test the complete rental and payment flow</li>
                    <li>Customize your site branding and content</li>
                </ol>
            </div>
            
            <div class="success-actions">
                <a href="../index.php" class="btn">🌐 Visit Your Site</a>
                <a href="../admin/login.php" class="btn btn-secondary">⚙️ Admin Login</a>
            </div>
            
            <div class="security-notice">
                <strong>🔒 Security Notice:</strong> Please delete the /install directory immediately for security reasons. You can do this via cPanel File Manager or FTP.
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
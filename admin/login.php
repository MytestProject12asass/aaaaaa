<?php
/**
 * StarRent.vip - Admin Login Page
 */

require_once '../config/config.php';

// Redirect if already logged in as admin
if (is_admin()) {
    redirect('/admin/dashboard.php');
}

$errors = [];

if ($_POST) {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required';
    } else {
        $db = Database::getInstance();
        $admin = $db->fetchOne(
            "SELECT * FROM admins WHERE email = ? AND status = 1", 
            [$email]
        );
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            
            redirect('/admin/dashboard.php');
        } else {
            $errors[] = 'Invalid email or password';
        }
    }
}

$pageTitle = 'Admin Login | StarRent.vip';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="text-4xl font-bold text-white mb-2">⚡ StarRent.vip</div>
                <h2 class="text-2xl font-bold text-white">Admin Panel</h2>
                <p class="mt-2 text-blue-200">Sign in to access the admin dashboard</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white py-8 px-6 shadow-2xl rounded-xl">
                <?php if ($errors): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="space-y-6">
                        <div>
                            <label class="form-label">Admin Email</label>
                            <div class="relative">
                                <input type="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       class="form-input pl-10" 
                                       placeholder="Enter admin email"
                                       required>
                                <i class="fas fa-user-shield absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Password</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       class="form-input pl-10" 
                                       placeholder="Enter password"
                                       required>
                                <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="remember" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label class="ml-2 block text-sm text-gray-900">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Sign In to Admin Panel
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Links -->
            <div class="text-center">
                <a href="/" class="text-blue-200 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
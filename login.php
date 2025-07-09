<?php
/**
 * StarRent.vip - User Login Page
 */

require_once 'config/config.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/dashboard.php');
}

$errors = [];
$success = '';

if ($_POST) {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required';
    } else {
        $user = new User();
        $userData = $user->authenticate($email, $password);
        
        if ($userData) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['name'];
            $_SESSION['user_email'] = $userData['email'];
            
            redirect('/dashboard.php');
        } else {
            $errors[] = 'Invalid email or password';
        }
    }
}

$pageTitle = 'Login | StarRent.vip';
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
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <a href="/" class="inline-block">
                    <h2 class="text-3xl font-bold text-blue-600">⚡ StarRent.vip</h2>
                </a>
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Sign in to your account</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Or 
                    <a href="/register.php" class="font-medium text-blue-600 hover:text-blue-500">
                        create a new account
                    </a>
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white py-8 px-6 shadow-lg rounded-lg">
                <?php if ($errors): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <form method="POST" data-validate>
                    <div class="space-y-6">
                        <div>
                            <label class="form-label">Email Address</label>
                            <div class="relative">
                                <input type="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       class="form-input pl-10" 
                                       placeholder="Enter your email"
                                       required>
                                <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Password</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       class="form-input pl-10" 
                                       placeholder="Enter your password"
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

                            <div class="text-sm">
                                <a href="/forgot-password.php" class="font-medium text-blue-600 hover:text-blue-500">
                                    Forgot your password?
                                </a>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Sign In
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Social Login (Optional) -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-google text-red-500"></i>
                            <span class="ml-2">Google</span>
                        </a>
                        <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fab fa-facebook text-blue-600"></i>
                            <span class="ml-2">Facebook</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="text-center text-sm text-gray-600">
                <p>
                    Don't have an account? 
                    <a href="/register.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign up now
                    </a>
                </p>
                <p class="mt-2">
                    <a href="/" class="text-blue-600 hover:text-blue-500">
                        ← Back to Homepage
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
</body>
</html>
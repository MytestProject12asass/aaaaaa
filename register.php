<?php
/**
 * StarRent.vip - User Registration Page
 */

require_once 'config/config.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('/dashboard.php');
}

$errors = [];
$success = '';

if ($_POST) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = 'Name, email, and password are required';
    }
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if ($password !== $password_confirmation) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (!$agree_terms) {
        $errors[] = 'You must agree to the terms and conditions';
    }
    
    if (empty($errors)) {
        $user = new User();
        
        // Check if email already exists
        if ($user->getByEmail($email)) {
            $errors[] = 'Email address is already registered';
        } else {
            // Create user
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'username' => $user->generateUsername($name)
            ];
            
            $userId = $user->create($userData);
            
            if ($userId) {
                $success = 'Account created successfully! You can now login.';
                // Clear form data
                $name = $email = $phone = '';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register | StarRent.vip';
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
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Create your account</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Or 
                    <a href="/login.php" class="font-medium text-blue-600 hover:text-blue-500">
                        sign in to existing account
                    </a>
                </p>
            </div>

            <!-- Registration Form -->
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
                    <p class="mt-2">
                        <a href="/login.php" class="font-medium underline">Click here to login</a>
                    </p>
                </div>
                <?php endif; ?>

                <form method="POST" data-validate>
                    <div class="space-y-6">
                        <div>
                            <label class="form-label">Full Name</label>
                            <div class="relative">
                                <input type="text" 
                                       name="name" 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                       class="form-input pl-10" 
                                       placeholder="Enter your full name"
                                       required>
                                <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

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
                            <label class="form-label">Phone Number (Optional)</label>
                            <div class="relative">
                                <input type="tel" 
                                       name="phone" 
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                                       class="form-input pl-10" 
                                       placeholder="Enter your phone number">
                                <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Password</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       class="form-input pl-10" 
                                       placeholder="Create a password"
                                       required>
                                <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                        </div>

                        <div>
                            <label class="form-label">Confirm Password</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password_confirmation" 
                                       class="form-input pl-10" 
                                       placeholder="Confirm your password"
                                       required>
                                <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="agree_terms" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   required>
                            <label class="ml-2 block text-sm text-gray-900">
                                I agree to the 
                                <a href="/terms-of-service" class="text-blue-600 hover:text-blue-500">Terms of Service</a>
                                and 
                                <a href="/privacy-policy" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                            </label>
                        </div>

                        <div>
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-user-plus mr-2"></i>
                                Create Account
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Social Registration (Optional) -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or register with</span>
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
                    Already have an account? 
                    <a href="/login.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign in here
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
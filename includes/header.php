<?php
if (!isset($pageTitle)) {
    $pageTitle = 'StarRent.vip - Premium Starlink Router Rentals';
}
?>
<header class="bg-white shadow-lg sticky top-0 z-50">
    <nav class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="/assets/images/logo.png" alt="StarRent.vip" class="h-8 w-auto mr-3">
                    <span class="text-xl font-bold text-gray-800">StarRent.vip</span>
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Home</a>
                <a href="/routers.php" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Routers</a>
                <a href="/about.php" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">About</a>
                <a href="/contact.php" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Contact</a>
                
                <?php if (is_logged_in()): ?>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        Account
                        <i class="fas fa-chevron-down ml-1 text-sm"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        <a href="/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        <a href="/rentals.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Rentals</a>
                        <a href="/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        <a href="/support.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Support</a>
                        <hr class="my-1">
                        <a href="/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
                <?php else: ?>
                <a href="/login.php" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Login</a>
                <a href="/register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Sign Up</a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button x-data @click="$dispatch('toggle-mobile-menu')" class="text-gray-700 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open" x-transition class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t">
                <a href="/" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Home</a>
                <a href="/routers.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Routers</a>
                <a href="/about.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">About</a>
                <a href="/contact.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Contact</a>
                
                <?php if (is_logged_in()): ?>
                <hr class="my-2">
                <a href="/dashboard.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
                <a href="/rentals.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">My Rentals</a>
                <a href="/profile.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Profile</a>
                <a href="/support.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Support</a>
                <a href="/logout.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Logout</a>
                <?php else: ?>
                <hr class="my-2">
                <a href="/login.php" class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">Login</a>
                <a href="/register.php" class="block px-3 py-2 bg-blue-600 text-white rounded-lg text-center">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
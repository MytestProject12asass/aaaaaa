<?php
/**
 * StarRent.vip - Admin Dashboard
 */

require_once '../config/config.php';
require_admin();

$db = Database::getInstance();

// Get dashboard statistics
$stats = [
    'total_users' => $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE is_banned = 0")['count'],
    'total_routers' => $db->fetchOne("SELECT COUNT(*) as count FROM routers WHERE status = 1")['count'],
    'active_rentals' => $db->fetchOne("SELECT COUNT(*) as count FROM rentals WHERE rental_status = 'active'")['count'],
    'total_revenue' => $db->fetchOne("SELECT SUM(total_cost) as revenue FROM rentals WHERE payment_status = 'paid'")['revenue'] ?? 0,
    'pending_rentals' => $db->fetchOne("SELECT COUNT(*) as count FROM rentals WHERE rental_status = 'pending'")['count'],
    'available_routers' => $db->fetchOne("SELECT COUNT(*) as count FROM routers WHERE availability_status = 'available'")['count']
];

// Recent rentals
$recentRentals = $db->fetchAll("
    SELECT r.*, u.name as user_name, rt.name as router_name 
    FROM rentals r 
    JOIN users u ON r.user_id = u.id 
    JOIN routers rt ON r.router_id = rt.id 
    ORDER BY r.created_at DESC 
    LIMIT 10
");

$pageTitle = 'Admin Dashboard | StarRent.vip';
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
<body class="bg-gray-100">
    <!-- Admin Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">⚡ StarRent.vip</h1>
                    <span class="ml-4 text-gray-500">Admin Panel</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="/admin/logout.php" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-wifi text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Routers</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['total_routers']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Rentals</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['active_rentals']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo format_currency($stats['total_revenue']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Rentals</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['pending_rentals']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Available Routers</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($stats['available_routers']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/admin/routers.php" class="btn-primary text-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Router
                    </a>
                    <a href="/admin/rentals.php" class="btn-secondary text-center">
                        <i class="fas fa-list mr-2"></i>
                        Manage Rentals
                    </a>
                    <a href="/admin/users.php" class="btn-secondary text-center">
                        <i class="fas fa-users mr-2"></i>
                        Manage Users
                    </a>
                    <a href="/admin/settings.php" class="btn-secondary text-center">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Rentals -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Rentals</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rental #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Router
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dates
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recentRentals as $rental): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($rental['rental_number']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($rental['user_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($rental['router_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo format_date($rental['start_date'], 'M j') . ' - ' . format_date($rental['end_date'], 'M j, Y'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo format_currency($rental['total_cost']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge status-<?php echo $rental['rental_status']; ?>">
                                    <?php echo ucfirst($rental['rental_status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
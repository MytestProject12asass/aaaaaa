<?php
/**
 * StarRent.vip - Browse Routers Page
 */

require_once 'config/config.php';

$router = new Router();
$search = $_GET['search'] ?? '';
$filters = [
    'search' => $search,
    'availability' => 'available'
];

$routers = $router->getAll($filters);

$pageTitle = 'Browse Starlink Routers | StarRent.vip';
$pageDescription = 'Browse our complete selection of Starlink router rentals. Find the perfect satellite internet solution for your needs.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Browse Starlink Routers</h1>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                    Find the perfect Starlink router for your connectivity needs. All models available for rent with flexible terms.
                </p>
            </div>
        </div>
    </section>

    <!-- Search & Filters -->
    <section class="py-8 bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex-1 max-w-md">
                    <form method="GET" class="relative">
                        <input type="text" 
                               name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Search routers..." 
                               class="form-input pl-10">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 btn-primary py-2 px-4">
                            Search
                        </button>
                    </form>
                </div>
                <div class="text-gray-600">
                    <?php echo count($routers); ?> router(s) available
                </div>
            </div>
        </div>
    </section>

    <!-- Routers Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <?php if (empty($routers)): ?>
            <div class="text-center py-16">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Routers Found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search criteria or browse all available routers.</p>
                <a href="/routers.php" class="btn-primary">View All Routers</a>
            </div>
            <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($routers as $router): ?>
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <img src="/assets/images/routers/<?php echo $router['photo']; ?>" 
                             alt="<?php echo htmlspecialchars($router['name']); ?>" 
                             class="w-full h-64 object-cover">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="status-badge status-<?php echo $router['availability_status']; ?>">
                                <?php echo ucfirst($router['availability_status']); ?>
                            </span>
                        </div>

                        <!-- Rating -->
                        <?php if ($router['rating'] > 0): ?>
                        <div class="absolute top-4 right-4 bg-white bg-opacity-90 px-3 py-1 rounded-full">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="font-semibold"><?php echo $router['rating']; ?></span>
                                <span class="text-xs text-gray-500 ml-1">(<?php echo $router['total_reviews']; ?>)</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($router['name']); ?></h3>
                        <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($router['model']); ?></p>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($router['description'], 0, 120)) . '...'; ?></p>
                        
                        <!-- Specifications -->
                        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                            <div>
                                <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                                <span class="text-gray-600"><?php echo $router['max_speed']; ?></span>
                            </div>
                            <div>
                                <i class="fas fa-home text-blue-500 mr-2"></i>
                                <span class="text-gray-600"><?php echo $router['coverage_area']; ?></span>
                            </div>
                            <div>
                                <i class="fas fa-weight text-blue-500 mr-2"></i>
                                <span class="text-gray-600"><?php echo $router['weight']; ?></span>
                            </div>
                            <div>
                                <i class="fas fa-bolt text-blue-500 mr-2"></i>
                                <span class="text-gray-600"><?php echo $router['power_consumption']; ?></span>
                            </div>
                        </div>
                        
                        <!-- Pricing -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Daily</span>
                                <span class="font-bold text-gray-900"><?php echo format_currency($router['daily_rate']); ?></span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Weekly</span>
                                <span class="font-bold text-gray-900"><?php echo format_currency($router['weekly_rate']); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Monthly</span>
                                <span class="font-bold text-primary-600"><?php echo format_currency($router['monthly_rate']); ?></span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="space-y-3">
                            <?php if ($router['availability_status'] === 'available'): ?>
                            <a href="/router.php?id=<?php echo $router['id']; ?>" 
                               class="btn-primary w-full text-center group">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Rent Now
                            </a>
                            <?php else: ?>
                            <button class="btn-secondary w-full" disabled>
                                <i class="fas fa-clock mr-2"></i>
                                Not Available
                            </button>
                            <?php endif; ?>
                            
                            <a href="/router.php?id=<?php echo $router['id']; ?>" 
                               class="w-full text-center py-2 text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
</body>
</html>
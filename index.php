<?php
/**
 * StarRent.vip - Starlink Router Rental Platform
 * Homepage
 */

require_once 'config/config.php';

// Check if installed
if (!file_exists('config/installed.lock')) {
    header('Location: install/index.php');
    exit;
}

$router = new Router();
$featuredRouters = $router->getFeatured(6);

// Get some stats for the hero section
$rental = new Rental();
$stats = $rental->getStats();

$pageTitle = 'Premium Starlink Router Rentals | StarRent.vip';
$pageDescription = 'Rent high-speed Starlink satellite internet routers for your home, business, or travel needs. Fast, reliable, and affordable with cryptocurrency payment options.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="starlink, router rental, satellite internet, high-speed internet, cryptocurrency payment">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 text-white py-20">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in-up">
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Rent Premium <span class="text-blue-300">Starlink</span> Routers
                    </h1>
                    <p class="text-xl mb-8 text-blue-100">
                        Get high-speed satellite internet anywhere in the world. Perfect for remote work, 
                        streaming, gaming, and staying connected on the go.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="/routers.php" class="btn-primary group">
                            <i class="fas fa-rocket mr-2"></i>
                            Browse Routers
                        </a>
                        <a href="#how-it-works" class="btn-outline group">
                            <i class="fas fa-play mr-2"></i>
                            How It Works
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-300"><?php echo number_format($stats['total_rentals']); ?>+</div>
                            <div class="text-sm text-blue-200">Happy Customers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-300">350+</div>
                            <div class="text-sm text-blue-200">Mbps Speed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-300">24/7</div>
                            <div class="text-sm text-blue-200">Support</div>
                        </div>
                    </div>
                </div>
                
                <div class="relative animate-slide-in-right">
                    <img src="https://images.pexels.com/photos/4792728/pexels-photo-4792728.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Starlink Router" class="rounded-2xl shadow-2xl">
                    <div class="absolute -bottom-6 -right-6 bg-green-500 text-white p-4 rounded-xl shadow-lg">
                        <div class="flex items-center">
                            <i class="fas fa-wifi text-2xl mr-3"></i>
                            <div>
                                <div class="font-bold">Ultra-Fast</div>
                                <div class="text-sm">Satellite Internet</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Routers -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Featured Router Models</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Choose from our premium selection of Starlink routers, each designed for specific use cases 
                    and environments. All models support high-speed satellite internet connectivity.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featuredRouters as $router): ?>
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <img src="/assets/images/routers/<?php echo $router['photo']; ?>" 
                             alt="<?php echo htmlspecialchars($router['name']); ?>" 
                             class="w-full h-64 object-cover">
                        <?php if ($router['featured']): ?>
                        <div class="absolute top-4 left-4 bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Featured
                        </div>
                        <?php endif; ?>
                        <div class="absolute top-4 right-4 bg-white bg-opacity-90 px-3 py-1 rounded-full">
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="font-semibold"><?php echo $router['rating']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($router['name']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($router['description'], 0, 100)) . '...'; ?></p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                            <div>
                                <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                                <span class="text-gray-600">Up to <?php echo $router['max_speed']; ?></span>
                            </div>
                            <div>
                                <i class="fas fa-home text-blue-500 mr-2"></i>
                                <span class="text-gray-600"><?php echo $router['coverage_area']; ?></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-2xl font-bold text-blue-600"><?php echo format_currency($router['daily_rate']); ?></span>
                                <span class="text-gray-500">/day</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Weekly: <?php echo format_currency($router['weekly_rate']); ?></div>
                                <div class="text-sm text-gray-500">Monthly: <?php echo format_currency($router['monthly_rate']); ?></div>
                            </div>
                        </div>
                        
                        <a href="/router.php?id=<?php echo $router['id']; ?>" 
                           class="btn-primary w-full text-center group">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Rent Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="/routers.php" class="btn-secondary group">
                    View All Routers
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="bg-gray-100 py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Simple steps to get your Starlink router rental</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center animate-fade-in-up">
                    <div class="bg-blue-500 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Choose Router</h3>
                    <p class="text-gray-600">Select the perfect Starlink router model for your needs and location.</p>
                </div>
                <div class="text-center animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="bg-blue-500 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Select Dates</h3>
                    <p class="text-gray-600">Pick your rental period and delivery preferences.</p>
                </div>
                <div class="text-center animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="bg-blue-500 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Pay with Crypto</h3>
                    <p class="text-gray-600">Secure payment with Bitcoin, Ethereum, or other cryptocurrencies.</p>
                </div>
                <div class="text-center animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="bg-blue-500 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Get Connected</h3>
                    <p class="text-gray-600">Receive your router and enjoy high-speed satellite internet anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Why Choose StarRent.vip?</h2>
                <p class="text-xl text-gray-600">Premium features that make us the best choice for Starlink rentals</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="text-center p-6 card-hover">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-rocket text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Ultra-Fast Internet</h3>
                    <p class="text-gray-600">Experience speeds up to 350 Mbps with low latency satellite internet connectivity.</p>
                </div>
                <div class="text-center p-6 card-hover">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Secure Payments</h3>
                    <p class="text-gray-600">Pay securely with cryptocurrency through our Plisio integration. Your transactions are protected.</p>
                </div>
                <div class="text-center p-6 card-hover">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Get help whenever you need it with our round-the-clock customer support team.</p>
                </div>
                <div class="text-center p-6 card-hover">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-truck text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Fast Delivery</h3>
                    <p class="text-gray-600">Quick delivery options including same-day delivery in major cities.</p>
                </div>
                <div class="text-center p-6 card-hover">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-globe text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Global Coverage</h3>
                    <p class="text-gray-600">Starlink works virtually anywhere on Earth with clear sky view.</p>
                </div>
                <div class="text-center p-6 card-hover">
                    <div class="feature-icon mx-auto mb-4">
                        <i class="fas fa-tools text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Easy Setup</h3>
                    <p class="text-gray-600">Simple plug-and-play setup with detailed instructions and video guides.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to Get Connected?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Join thousands of satisfied customers who trust StarRent.vip for their satellite internet needs. 
                Start your rental today and experience the freedom of high-speed internet anywhere.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/routers.php" class="btn bg-white text-blue-600 hover:bg-gray-100 group">
                    <i class="fas fa-search mr-2"></i>
                    Browse Routers
                </a>
                <a href="/contact.php" class="btn border-2 border-white text-white hover:bg-white hover:text-blue-600 group">
                    <i class="fas fa-phone mr-2"></i>
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
</body>
</html>
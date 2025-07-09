/*
  # Insert Sample Data for StarRent.vip

  1. Sample Data
    - Router features
    - Payment methods
    - Sample routers
    - Sample users (for demo)

  2. Configuration
    - Default payment methods for Plisio
    - Router feature categories
    - Sample router inventory
*/

-- Insert router features
INSERT INTO router_features (name, description, icon, category) VALUES
('High-Speed Internet', 'Up to 350 Mbps download speeds', 'wifi', 'performance'),
('Low Latency', 'Gaming-optimized with low latency', 'zap', 'performance'),
('Weather Resistant', 'IP54 rated for outdoor use', 'cloud-rain', 'durability'),
('Easy Setup', 'Plug and play installation', 'settings', 'usability'),
('Mobile App Control', 'Manage settings via mobile app', 'smartphone', 'usability'),
('24/7 Support', 'Round-the-clock customer support', 'headphones', 'support'),
('Global Coverage', 'Works anywhere with Starlink coverage', 'globe', 'coverage'),
('Unlimited Data', 'No data caps or throttling', 'infinity', 'data'),
('Portable Design', 'Lightweight and travel-friendly', 'briefcase', 'portability'),
('Enterprise Grade', 'Business-class reliability', 'building', 'business'),
('Priority Data', 'Business priority network access', 'star', 'business'),
('Advanced Security', 'Enterprise security features', 'shield', 'security');

-- Insert payment methods (Plisio supported cryptocurrencies)
INSERT INTO payment_methods (name, code, type, symbol, min_amount, max_amount, fee_percentage, confirmation_blocks, network, status, sort_order) VALUES
('Bitcoin', 'BTC', 'cryptocurrency', '₿', 0.0001, 10.0000, 0.0050, 1, 'Bitcoin', 'active', 1),
('Ethereum', 'ETH', 'cryptocurrency', 'Ξ', 0.001, 100.0000, 0.0030, 12, 'Ethereum', 'active', 2),
('Tether USD', 'USDT', 'stablecoin', '₮', 1.00, 50000.00, 0.0025, 12, 'Ethereum', 'active', 3),
('USD Coin', 'USDC', 'stablecoin', '$', 1.00, 50000.00, 0.0025, 12, 'Ethereum', 'active', 4),
('Litecoin', 'LTC', 'cryptocurrency', 'Ł', 0.01, 1000.00, 0.0040, 6, 'Litecoin', 'active', 5),
('Bitcoin Cash', 'BCH', 'cryptocurrency', '₿', 0.001, 1000.00, 0.0040, 6, 'Bitcoin Cash', 'active', 6),
('Dogecoin', 'DOGE', 'cryptocurrency', 'Ð', 1.00, 100000.00, 0.0050, 6, 'Dogecoin', 'active', 7),
('Monero', 'XMR', 'cryptocurrency', 'ɱ', 0.01, 1000.00, 0.0060, 10, 'Monero', 'active', 8),
('Dash', 'DASH', 'cryptocurrency', 'Đ', 0.01, 1000.00, 0.0050, 6, 'Dash', 'active', 9),
('Zcash', 'ZEC', 'cryptocurrency', 'ⓩ', 0.01, 1000.00, 0.0050, 6, 'Zcash', 'active', 10);

-- Insert sample routers
INSERT INTO routers (
  name, model, description, daily_rate, weekly_rate, monthly_rate, deposit_required,
  max_speed, coverage_area, weight, dimensions, power_consumption, operating_temperature,
  features, images, primary_image, status, location, serial_number, purchase_date, warranty_expiry
) VALUES
(
  'Starlink Standard Kit',
  'Gen 2 Standard',
  'Perfect for residential use and small businesses. Reliable high-speed internet with easy setup and weather-resistant design.',
  25.00, 150.00, 599.00, 500.00,
  '150 Mbps', '50km radius', '4.2 kg', '59.5 x 38.5 x 5.5 cm', '50-75W', '-30°C to +50°C',
  ARRAY['High-Speed Internet', 'Weather Resistant', 'Easy Setup', 'Mobile App Control', 'Global Coverage', 'Unlimited Data'],
  ARRAY[
    'https://images.pexels.com/photos/4792728/pexels-photo-4792728.jpeg?auto=compress&cs=tinysrgb&w=800',
    'https://images.pexels.com/photos/4792729/pexels-photo-4792729.jpeg?auto=compress&cs=tinysrgb&w=800'
  ],
  'https://images.pexels.com/photos/4792728/pexels-photo-4792728.jpeg?auto=compress&cs=tinysrgb&w=800',
  'available', 'San Francisco Warehouse', 'SL-STD-001', '2024-01-15', '2026-01-15'
),
(
  'Starlink Business Kit',
  'High Performance',
  'Enterprise-grade solution with priority data access and enhanced performance for demanding business applications.',
  45.00, 270.00, 1099.00, 1000.00,
  '350 Mbps', '100km radius', '5.8 kg', '59.5 x 38.5 x 5.5 cm', '110-150W', '-30°C to +50°C',
  ARRAY['High-Speed Internet', 'Low Latency', 'Priority Data', 'Enterprise Grade', 'Advanced Security', '24/7 Support'],
  ARRAY[
    'https://images.pexels.com/photos/4792729/pexels-photo-4792729.jpeg?auto=compress&cs=tinysrgb&w=800',
    'https://images.pexels.com/photos/4792730/pexels-photo-4792730.jpeg?auto=compress&cs=tinysrgb&w=800'
  ],
  'https://images.pexels.com/photos/4792729/pexels-photo-4792729.jpeg?auto=compress&cs=tinysrgb&w=800',
  'available', 'San Francisco Warehouse', 'SL-BIZ-001', '2024-01-20', '2026-01-20'
),
(
  'Starlink Mobile Kit',
  'Portable',
  'Compact and portable solution perfect for travelers, RVs, boats, and mobile applications with quick setup.',
  35.00, 210.00, 799.00, 750.00,
  '200 Mbps', '75km radius', '3.8 kg', '48.5 x 30.5 x 4.8 cm', '45-65W', '-30°C to +50°C',
  ARRAY['High-Speed Internet', 'Portable Design', 'Easy Setup', 'Weather Resistant', 'Mobile App Control', 'Global Coverage'],
  ARRAY[
    'https://images.pexels.com/photos/4792730/pexels-photo-4792730.jpeg?auto=compress&cs=tinysrgb&w=800',
    'https://images.pexels.com/photos/4792728/pexels-photo-4792728.jpeg?auto=compress&cs=tinysrgb&w=800'
  ],
  'https://images.pexels.com/photos/4792730/pexels-photo-4792730.jpeg?auto=compress&cs=tinysrgb&w=800',
  'rented', 'San Francisco Warehouse', 'SL-MOB-001', '2024-02-01', '2026-02-01'
),
(
  'Starlink Maritime Kit',
  'Maritime High Performance',
  'Specialized solution for maritime applications with enhanced stability and performance on moving vessels.',
  65.00, 390.00, 1499.00, 1500.00,
  '350 Mbps', '150km radius', '7.2 kg', '59.5 x 38.5 x 8.5 cm', '120-180W', '-40°C to +60°C',
  ARRAY['High-Speed Internet', 'Low Latency', 'Weather Resistant', 'Enterprise Grade', 'Priority Data', 'Advanced Security'],
  ARRAY[
    'https://images.pexels.com/photos/4792731/pexels-photo-4792731.jpeg?auto=compress&cs=tinysrgb&w=800',
    'https://images.pexels.com/photos/4792732/pexels-photo-4792732.jpeg?auto=compress&cs=tinysrgb&w=800'
  ],
  'https://images.pexels.com/photos/4792731/pexels-photo-4792731.jpeg?auto=compress&cs=tinysrgb&w=800',
  'available', 'Miami Warehouse', 'SL-MAR-001', '2024-02-10', '2026-02-10'
),
(
  'Starlink RV Kit',
  'RV Optimized',
  'Designed specifically for RV and camping enthusiasts with easy mounting and optimal performance on the road.',
  30.00, 180.00, 699.00, 600.00,
  '180 Mbps', '60km radius', '4.0 kg', '52.5 x 35.5 x 5.0 cm', '50-80W', '-25°C to +55°C',
  ARRAY['High-Speed Internet', 'Portable Design', 'Easy Setup', 'Weather Resistant', 'Mobile App Control', 'Unlimited Data'],
  ARRAY[
    'https://images.pexels.com/photos/4792733/pexels-photo-4792733.jpeg?auto=compress&cs=tinysrgb&w=800',
    'https://images.pexels.com/photos/4792734/pexels-photo-4792734.jpeg?auto=compress&cs=tinysrgb&w=800'
  ],
  'https://images.pexels.com/photos/4792733/pexels-photo-4792733.jpeg?auto=compress&cs=tinysrgb&w=800',
  'available', 'Denver Warehouse', 'SL-RV-001', '2024-02-15', '2026-02-15'
),
(
  'Starlink Aviation Kit',
  'Aviation High Performance',
  'Premium solution for aviation applications with certified hardware and global coverage for aircraft.',
  85.00, 510.00, 1999.00, 2000.00,
  '400 Mbps', '200km radius', '8.5 kg', '65.5 x 42.5 x 9.0 cm', '150-220W', '-50°C to +70°C',
  ARRAY['High-Speed Internet', 'Low Latency', 'Enterprise Grade', 'Priority Data', 'Advanced Security', 'Global Coverage'],
  ARRAY[
    'https://images.pexels.com/photos/4792735/pexels-photo-4792735.jpeg?auto=compress&cs=tinysrgb&w=800',
    'https://images.pexels.com/photos/4792736/pexels-photo-4792736.jpeg?auto=compress&cs=tinysrgb&w=800'
  ],
  'https://images.pexels.com/photos/4792735/pexels-photo-4792735.jpeg?auto=compress&cs=tinysrgb&w=800',
  'maintenance', 'Los Angeles Warehouse', 'SL-AVI-001', '2024-03-01', '2026-03-01'
);

-- Update router ratings and review counts (simulated)
UPDATE routers SET rating = 4.8, review_count = 124 WHERE name = 'Starlink Standard Kit';
UPDATE routers SET rating = 4.9, review_count = 89 WHERE name = 'Starlink Business Kit';
UPDATE routers SET rating = 4.7, review_count = 156 WHERE name = 'Starlink Mobile Kit';
UPDATE routers SET rating = 4.6, review_count = 67 WHERE name = 'Starlink Maritime Kit';
UPDATE routers SET rating = 4.8, review_count = 203 WHERE name = 'Starlink RV Kit';
UPDATE routers SET rating = 4.9, review_count = 45 WHERE name = 'Starlink Aviation Kit';

-- Insert sample demo user (for testing purposes)
INSERT INTO users (
  email, name, username, phone, address, city, country, postal_code,
  balance, email_verified, phone_verified, status, referral_code
) VALUES (
  'demo@star-rent.vip',
  'Demo User',
  'demouser',
  '+1-555-0123',
  '123 Demo Street',
  'San Francisco',
  'United States',
  '94105',
  1000.00,
  true,
  true,
  'active',
  'DEMO2024'
);

-- Create some sample notifications for demo user
INSERT INTO notifications (user_id, type, title, message, data) 
SELECT 
  u.id,
  'welcome',
  'Welcome to StarRent!',
  'Thank you for joining StarRent. Start browsing our premium Starlink router collection.',
  '{"action": "browse_routers"}'
FROM users u WHERE u.email = 'demo@star-rent.vip';

INSERT INTO notifications (user_id, type, title, message, data)
SELECT 
  u.id,
  'promotion',
  'Special Offer: 20% Off First Rental',
  'Use code WELCOME20 to get 20% off your first router rental. Valid for 30 days.',
  '{"code": "WELCOME20", "discount": 20, "expires": "2024-12-31"}'
FROM users u WHERE u.email = 'demo@star-rent.vip';
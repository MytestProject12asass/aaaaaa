# StarRent.vip - Starlink Router Rental Platform

A comprehensive PHP-based platform for renting Starlink satellite internet routers with cryptocurrency payment integration via Plisio.net.

## 🚀 Features

### Core Functionality
- **Router Rental System**: Complete booking and rental management
- **Cryptocurrency Payments**: Secure payments via Plisio.net (BTC, ETH, USDT, USDC, and 15+ more)
- **User Management**: Customer registration, authentication, and profiles
- **Admin Dashboard**: Complete administrative control panel
- **Support System**: Integrated ticketing and customer support
- **Review System**: Customer ratings and feedback

### Technical Features
- **MySQL Database**: Robust relational database structure
- **Responsive Design**: Mobile-first responsive interface
- **Security**: CSRF protection, password hashing, input validation
- **Email Integration**: SMTP email notifications
- **File Upload**: Secure file handling for images and documents
- **API Integration**: RESTful API endpoints for frontend interactions

## 📋 Requirements

### Server Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache or Nginx
- **SSL Certificate**: Required for production (cryptocurrency payments)

### PHP Extensions
- PDO MySQL
- cURL
- GD or ImageMagick
- OpenSSL
- JSON
- mbstring

### Third-Party Services
- **Plisio.net Account**: For cryptocurrency payment processing
- **SMTP Server**: For email notifications (Gmail, SendGrid, etc.)

## 🛠 Installation Instructions for cPanel

### Step 1: Download and Upload Files

1. **Download the project files** and create a ZIP archive
2. **Login to your cPanel** hosting account
3. **Navigate to File Manager**
4. **Upload the ZIP file** to your domain's public_html directory
5. **Extract the files** in the public_html directory

### Step 2: Create MySQL Database

1. **Go to MySQL Databases** in cPanel
2. **Create a new database** (e.g., `your_username_starrent`)
3. **Create a database user** with a strong password
4. **Add the user to the database** with ALL PRIVILEGES
5. **Note down the database credentials**:
   - Database Host: `localhost`
   - Database Name: `your_username_starrent`
   - Username: `your_username_dbuser`
   - Password: `your_secure_password`

### Step 3: Run the Installation Wizard

1. **Navigate to**: `https://yourdomain.com/install/index.php`
2. **Follow the 6-step installation wizard**:
   - **Step 1**: Welcome screen with system requirements check
   - **Step 2**: Enter database credentials and test connection
   - **Step 3**: Import database structure and sample data
   - **Step 4**: Configure site settings and admin account
   - **Step 5**: Finalize installation and create directories
   - **Step 6**: Installation success with next steps

### Step 4: Post-Installation Setup

1. **Delete the install directory** for security:
   ```bash
   rm -rf /public_html/install/
   ```

2. **Set proper file permissions**:
   - Directories: 755
   - Files: 644
   - uploads/ directory: 755 (writable)

3. **Configure SSL certificate** (required for payments)

4. **Set up SMTP email** in admin panel

5. **Upload router images** to `/assets/images/routers/`

## 🔧 Configuration

### Environment Configuration

The installation wizard will automatically configure your database settings. For manual configuration, edit `config/database.php`:

```php
private $host = 'localhost';
private $username = 'your_db_username';
private $password = 'your_db_password';
private $database = 'your_db_name';
```

### Plisio Configuration

Configure Plisio.net API keys during installation or in the admin panel:

```php
// These are set during installation
define('PLISIO_API_KEY', 'your_api_key');
define('PLISIO_SECRET_KEY', 'your_secret_key');
```

## 📁 Directory Structure

```
/public_html/
├── config/           # Configuration files
│   ├── config.php         # Main configuration
│   ├── database.php       # Database configuration
│   └── installed.lock     # Installation lock file
├── classes/          # PHP classes
│   ├── Router.php         # Router model
│   ├── User.php          # User model
│   ├── Rental.php        # Rental model
│   ├── Payment.php       # Payment model
│   └── PlisioAPI.php     # Plisio integration
├── includes/         # Common includes
│   ├── header.php        # Site header
│   └── footer.php        # Site footer
├── assets/           # Static assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── images/           # Images
├── uploads/          # User uploads
│   ├── routers/          # Router images
│   ├── users/            # User avatars
│   └── tickets/          # Support attachments
├── database/         # Database files
│   └── starlink_rental.sql # Database structure
├── install/          # Installation script
│   └── index.php         # Installation wizard
├── admin/            # Admin panel
└── index.php         # Homepage
```

## 🔐 Security Features

- **CSRF Protection**: All forms protected against CSRF attacks
- **Password Hashing**: Secure password storage using PHP's password_hash()
- **Input Validation**: Comprehensive input sanitization and validation
- **SQL Injection Prevention**: Prepared statements for all database queries
- **File Upload Security**: Restricted file types and secure upload handling
- **Session Security**: Secure session configuration

## 💳 Payment Integration

### Supported Cryptocurrencies
- Bitcoin (BTC)
- Ethereum (ETH)
- Litecoin (LTC)
- Bitcoin Cash (BCH)
- Tether (USDT)
- USD Coin (USDC)
- Shiba Inu (SHIB)
- Dogecoin (DOGE)
- TRON (TRX)
- Binance Coin (BNB)
- And 8+ more currencies

### Payment Flow
1. Customer selects router and rental period
2. System calculates total cost including security deposit
3. Customer chooses cryptocurrency for payment
4. Plisio generates payment invoice
5. Customer completes payment
6. Webhook confirms payment
7. Rental is automatically confirmed

## 🎨 Customization

### Themes and Styling
- Built with Tailwind CSS for easy customization
- Custom CSS in `assets/css/style.css`
- Responsive design with mobile-first approach

### Adding New Router Models
1. Login to admin panel
2. Navigate to Routers → Add New
3. Fill in router details and specifications
4. Upload high-quality images
5. Set pricing for daily, weekly, and monthly rates

## 📊 Admin Features

### Dashboard
- Revenue analytics
- Rental statistics
- Recent bookings
- Payment status overview

### Router Management
- Add/edit/delete router models
- Manage availability
- Update pricing
- Upload images and specifications

### User Management
- View customer accounts
- Manage user permissions
- Handle account verification
- Process refunds

### Rental Management
- View all rentals
- Update rental status
- Process extensions
- Handle returns

### Support System
- Ticket management
- Customer communication
- File attachments
- Priority levels

## 🔧 Maintenance

### Regular Tasks
- **Database Backups**: Schedule regular MySQL backups
- **Log Monitoring**: Check error logs regularly
- **Security Updates**: Keep PHP and dependencies updated
- **Performance Monitoring**: Monitor site performance and optimize

### Troubleshooting

#### Common Issues
1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Verify database server is running
   - Check user permissions

2. **Payment Issues**
   - Verify Plisio API credentials
   - Check webhook URL configuration
   - Ensure SSL certificate is valid

3. **Email Not Sending**
   - Verify SMTP settings in admin panel
   - Check email credentials
   - Test with different SMTP provider

## 📞 Support

### Documentation
- Installation guide (this file)
- Database schema documentation
- API integration guides

### Getting Help
- Check the FAQ section in admin panel
- Review error logs in cPanel
- Contact hosting provider for server issues

## 🚀 Going Live

### Pre-Launch Checklist
- [ ] SSL certificate installed and working
- [ ] Database properly configured and secured
- [ ] Plisio account verified and API keys configured
- [ ] SMTP email working correctly
- [ ] Router inventory added with images
- [ ] Test complete rental flow
- [ ] Admin account secured with strong password
- [ ] Install directory deleted
- [ ] File permissions set correctly
- [ ] Backup system in place

### SEO Optimization
- Update meta titles and descriptions
- Configure Google Analytics
- Submit sitemap to search engines
- Optimize images for web
- Set up Google Search Console

## 📄 License

This project is proprietary software. All rights reserved.

## 🤝 Contributing

This is a commercial project. For customization requests or feature additions, please contact the development team.

---

**StarRent.vip** - Premium Starlink Router Rentals with Cryptocurrency Payments

## 🎯 Quick Start Guide

1. **Upload files** to your cPanel hosting
2. **Create MySQL database** in cPanel
3. **Visit** `/install/index.php` to start installation
4. **Follow the 6-step wizard** - takes less than 5 minutes!
5. **Delete** `/install/` directory for security
6. **Start accepting** cryptocurrency payments for router rentals!

The platform is now **production-ready** and can be deployed immediately on any cPanel hosting provider!
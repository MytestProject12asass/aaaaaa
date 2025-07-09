/**
 * StarRent.vip Main JavaScript
 * Starlink Router Rental Platform
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Initialize components
    initializeDatePickers();
    initializeTooltips();
    initializeLazyLoading();
    initializeFormValidation();
    initializeNotifications();
    initializeCryptoPayments();
    
    // Add smooth scrolling to anchor links
    initializeSmoothScrolling();
    
    // Initialize mobile menu
    initializeMobileMenu();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize rental calculator
    initializeRentalCalculator();
}

// Date picker initialization
function initializeDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('min', today);
        
        // Add change event listener
        input.addEventListener('change', function() {
            validateDateRange(this);
        });
    });
}

// Validate date ranges
function validateDateRange(input) {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate && startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        
        if (end <= start) {
            showNotification('End date must be after start date', 'error');
            endDate.value = '';
        } else {
            // Calculate rental duration and update pricing
            updateRentalPricing();
        }
    }
}

// Tooltip initialization
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const text = event.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip-popup';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    
    event.target._tooltip = tooltip;
}

function hideTooltip(event) {
    if (event.target._tooltip) {
        document.body.removeChild(event.target._tooltip);
        delete event.target._tooltip;
    }
}

// Lazy loading for images
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-image');
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            img.classList.add('lazy-image');
            imageObserver.observe(img);
        });
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.add('loaded');
        });
    }
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!validateForm(this)) {
                event.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let message = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'This field is required';
    }
    
    // Email validation
    else if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'Please enter a valid email address';
        }
    }
    
    // Phone validation
    else if (field.name === 'phone' && value) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        if (!phoneRegex.test(value.replace(/\s/g, ''))) {
            isValid = false;
            message = 'Please enter a valid phone number';
        }
    }
    
    // Password validation
    else if (type === 'password' && value) {
        if (value.length < 8) {
            isValid = false;
            message = 'Password must be at least 8 characters long';
        }
    }
    
    // Password confirmation
    else if (field.name === 'password_confirmation' && value) {
        const password = document.querySelector('input[name="password"]');
        if (password && value !== password.value) {
            isValid = false;
            message = 'Passwords do not match';
        }
    }
    
    // Display validation result
    displayFieldValidation(field, isValid, message);
    
    return isValid;
}

function displayFieldValidation(field, isValid, message) {
    // Remove existing validation messages
    const existingError = field.parentNode.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Update field styling
    field.classList.remove('border-red-500', 'border-green-500');
    
    if (!isValid && message) {
        field.classList.add('border-red-500');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    } else if (isValid && field.value.trim()) {
        field.classList.add('border-green-500');
    }
}

// Notification system
function initializeNotifications() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }
}

function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    
    const typeClasses = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    notification.className = `${typeClasses[type]} px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform translate-x-full transition-transform duration-300`;
    notification.innerHTML = `
        <i class="${icons[type]}"></i>
        <span>${message}</span>
        <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentNode.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    if (duration > 0) {
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }
}

// Cryptocurrency payment handling
function initializeCryptoPayments() {
    const cryptoButtons = document.querySelectorAll('[data-crypto-payment]');
    cryptoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currency = this.dataset.cryptoPayment;
            const amount = this.dataset.amount;
            const orderId = this.dataset.orderId;
            
            initiateCryptoPayment(currency, amount, orderId);
        });
    });
}

function initiateCryptoPayment(currency, amount, orderId) {
    // Show loading state
    showNotification('Initiating payment...', 'info');
    
    // Make API call to create payment
    fetch('/api/create-payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({
            currency: currency,
            amount: amount,
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to payment page
            window.location.href = data.payment_url;
        } else {
            showNotification(data.message || 'Payment initiation failed', 'error');
        }
    })
    .catch(error => {
        console.error('Payment error:', error);
        showNotification('Payment initiation failed', 'error');
    });
}

// Smooth scrolling
function initializeSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(event) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                event.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Mobile menu
function initializeMobileMenu() {
    const mobileMenuButton = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.querySelector('[data-search]');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });
    }
}

function performSearch(query) {
    if (query.length < 2) return;
    
    fetch(`/api/search.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
}

function displaySearchResults(results) {
    const resultsContainer = document.getElementById('search-results');
    if (!resultsContainer) return;
    
    if (results.length === 0) {
        resultsContainer.innerHTML = '<p class="text-gray-500 p-4">No results found</p>';
        return;
    }
    
    const html = results.map(result => `
        <div class="p-4 border-b hover:bg-gray-50">
            <h3 class="font-semibold">${result.title}</h3>
            <p class="text-gray-600 text-sm">${result.description}</p>
            <a href="${result.url}" class="text-blue-600 text-sm">View details</a>
        </div>
    `).join('');
    
    resultsContainer.innerHTML = html;
}

// Rental calculator
function initializeRentalCalculator() {
    const calculatorForm = document.getElementById('rental-calculator');
    if (calculatorForm) {
        const inputs = calculatorForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', updateRentalPricing);
        });
    }
}

function updateRentalPricing() {
    const routerId = document.getElementById('router_id')?.value;
    const startDate = document.getElementById('start_date')?.value;
    const endDate = document.getElementById('end_date')?.value;
    const rentalType = document.getElementById('rental_type')?.value;
    
    if (!routerId || !startDate || !endDate) return;
    
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    
    if (days <= 0) return;
    
    // Make API call to calculate pricing
    fetch('/api/calculate-pricing.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            router_id: routerId,
            rental_type: rentalType,
            days: days
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updatePricingDisplay(data.pricing);
        }
    })
    .catch(error => {
        console.error('Pricing calculation error:', error);
    });
}

function updatePricingDisplay(pricing) {
    const elements = {
        days: document.getElementById('rental-days'),
        subtotal: document.getElementById('rental-subtotal'),
        deposit: document.getElementById('security-deposit'),
        total: document.getElementById('total-cost')
    };
    
    if (elements.days) elements.days.textContent = pricing.days;
    if (elements.subtotal) elements.subtotal.textContent = formatCurrency(pricing.subtotal);
    if (elements.deposit) elements.deposit.textContent = formatCurrency(pricing.deposit);
    if (elements.total) elements.total.textContent = formatCurrency(pricing.total);
}

// Utility functions
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    return new Intl.DateTimeFormat('en-US', { ...defaultOptions, ...options }).format(new Date(date));
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied to clipboard', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied to clipboard', 'success');
    }
}

// Export functions for global use
window.StarRent = {
    showNotification,
    copyToClipboard,
    formatCurrency,
    formatDate,
    updateRentalPricing,
    initiateCryptoPayment
};
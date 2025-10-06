setTimeout(function() {
    var currentPage = window.location.pathname.split("/").pop();
    var navLinks = document.querySelectorAll('.navlink');
    navLinks.forEach(function(link) {
        link.style.color = ''; 
        
        if (currentPage === '1home.html') {
            if (link.id === 'home-link') {
                link.style.color = 'red'; 
            }
        } else if (currentPage === '1catalogue.html') {
            if (link.id === 'catalogue-link') {
                link.style.color = 'red'; 
            }
        } else if (currentPage === '1contact.html') {
            if (link.id === 'contact-link') {
                link.style.color = 'red'; 
            }
        } else if (currentPage === '1about.html') {
            if (link.id === 'about-link') {
                link.style.color = 'red'; 
            }
        }
    });

var aboutLink = document.getElementById('about-link');
var fitemLink = document.getElementById('fItems-link');

if (aboutLink) {
    aboutLink.addEventListener('click', function() {
        var homeLink = document.getElementById('home-link');
        if (homeLink) {
            homeLink.style.color = '';
            if (fitemLink) {
                fitemLink.style.color = '';
            }
        }
        aboutLink.style.color = 'red';
    });
}

if (fitemLink) {
    fitemLink.addEventListener('click', function() {
        var homeLink = document.getElementById('home-link');
        if (homeLink) {
            homeLink.style.color = '';
            if (aboutLink) {
                aboutLink.style.color = '';
            }
        }
        fitemLink.style.color = 'red';
    });
}

    const paymentButton = document.getElementById('paymentButton');
    if (paymentButton) {
        paymentButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Check if this is a cart checkout
            const urlParams = new URLSearchParams(window.location.search);
            const isCartCheckout = urlParams.get('cart') === '1';
            
            if (isCartCheckout) {
                // Clear cart after successful payment
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_cart'
                })
                .then(() => {
                    alert('Your payment is successful! Your cart has been cleared.');
                    window.location.href = '1home.php';
                })
                .catch(() => {
                    alert('Your payment is successful!');
                    window.location.href = '1home.php';
                });
            } else {
                alert('Your payment is successful!');
                window.location.href = '1home.php';
            }
        });
    }

    const selectedImages = document.querySelectorAll('.img');

    selectedImages.forEach(image => {
        image.addEventListener('click', () => {
            window.location.href = 'payment.html';
        });
    });

    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const messageDiv = document.getElementById('register-message');

            // Basic validation
            if (!username || !email || !password) {
                showMessage('Please fill in all fields.', 'error');
                return;
            }

            if (password.length < 6) {
                showMessage('Password must be at least 6 characters long.', 'error');
                return;
            }

            // If validation passes
            showMessage('Registration successful! Welcome, ' + username + '!', 'success');

            // Here you would typically send the data to your server
            console.log('Registration data:', { username, email, password });

            // Clear form after success
            setTimeout(() => {
                this.reset();
                hideMessage();
            }, 3000);
        });

        function showMessage(text, type) {
            const messageDiv = document.getElementById('register-message');
            messageDiv.textContent = text;
            messageDiv.className = 'register-message ' + type;
            messageDiv.style.display = 'block';
        }

        function hideMessage() {
            const messageDiv = document.getElementById('register-message');
            messageDiv.style.display = 'none';
        }

        // Add interactive effects
        const inputs = document.querySelectorAll('.register-form-group input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    }

    // Login form functionality
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('login-message');

            // Simple validation
            if (email && password) {
                // Show success message
                messageDiv.className = 'login-message success';
                messageDiv.textContent = 'Login successful! Redirecting...';
                messageDiv.style.display = 'block';

                // Simulate redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '1home.html';
                }, 2000);
            } else {
                // Show error message
                messageDiv.className = 'login-message error';
                messageDiv.textContent = 'Please fill in all fields.';
                messageDiv.style.display = 'block';
            }
        });

        // Hide message when user starts typing
        document.getElementById('email').addEventListener('input', hideMessage);
        document.getElementById('password').addEventListener('input', hideMessage);

        function hideMessage() {
            const messageDiv = document.getElementById('login-message');
            messageDiv.style.display = 'none';
        }
    }
}, 500);

function toggleDropdown() {
            const dropdown = document.getElementById('dropdownContent');
            const arrow = document.getElementById('dropdownArrow');
            
            dropdown.classList.toggle('show');
            arrow.classList.toggle('open');
        }

        // Function to select option
        function selectOption(optionText) {
            const selectedOption = document.getElementById('selectedOption');
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            
            // Remove selected class from all items
            dropdownItems.forEach(item => item.classList.remove('selected'));
            
            // Update selected option text
            selectedOption.textContent = optionText;
            
            // Add selected class to clicked item
            event.target.classList.add('selected');
            
            // Close dropdown
            toggleDropdown();
            
            // You can add your sorting logic here
            console.log('Selected:', optionText);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.querySelector('.sort-dropdown');
            if (!dropdown.contains(event.target)) {
                document.getElementById('dropdownContent').classList.remove('show');
                document.getElementById('dropdownArrow').classList.remove('open');
            }
        });

        // Handle dropdown arrow behavior for select elements
        document.addEventListener('DOMContentLoaded', function() {
            const selectElements = document.querySelectorAll('.category-select');
            
            selectElements.forEach(function(select) {
                const arrow = select.nextElementSibling;
                
                // Initially set arrow to down
                if (arrow && arrow.classList.contains('select-arrow')) {
                    arrow.style.transform = 'none';
                }
                
                // Handle click - show up arrow when opening dropdown
                select.addEventListener('mousedown', function() {
                    if (arrow && arrow.classList.contains('select-arrow')) {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                });
                
                // Handle selection change - immediately show down arrow after selecting
                select.addEventListener('change', function() {
                    if (arrow && arrow.classList.contains('select-arrow')) {
                        arrow.style.transform = 'none';
                    }
                });
            });
        });

// Add to cart functionality
function addToCart(itemId) {
    // Create form data
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('item_id', itemId);
    
    // Send AJAX request
    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Item added to cart!', 'success');
            // Update cart count if you have a cart counter in header
            updateCartCount(data.cart_count);
        } else {
            showNotification('Failed to add item to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Notification system
function showNotification(message, type) {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease;
        max-width: 300px;
        text-align: center;
    `;
    
    if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)';
    } else {
        notification.style.background = 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)';
    }
    
    // Add CSS for animation if not already present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Add to body
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Update cart count (placeholder function)
function updateCartCount(count) {
    const cartCounter = document.querySelector('.cart-count');
    if (cartCounter) {
        cartCounter.textContent = count;
    }
}
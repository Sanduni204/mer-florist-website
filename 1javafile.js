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

    document.getElementById('paymentButton').addEventListener('click', function() {
        alert('Your payment is successful!');
    });

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

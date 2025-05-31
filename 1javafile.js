document.addEventListener("DOMContentLoaded", function() {
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
    var aboutLink = document.getElementById('about-link');
    if (aboutLink) {
        aboutLink.addEventListener('click', function() {
            var homeLink = document.getElementById('home-link');
            if (homeLink) {
                homeLink.style.color = ''; 
                fitemLink.style.color = ''; 
            }
            aboutLink.style.color = 'red'; 
        });
    }

 
 
var fitemLink = document.getElementById('fItems-link');
if (fitemLink) {
    fitemLink.addEventListener('click', function() {
        var homeLink = document.getElementById('home-link');
        if (homeLink) {
            homeLink.style.color = '';
            aboutLink.style.color = '';
        }
        fitemLink.style.color = 'red'; 
    });
}
});

document.getElementById('paymentButton').addEventListener('click', function() {
    alert('Your payment is successful!');
});

const selectedImages = document.querySelectorAll('.img');


selectedImages.forEach(image => {
    image.addEventListener('click', () => {
        
        window.location.href = 'payment.html';
    });
});





  

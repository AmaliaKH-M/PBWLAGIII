// KosMarket JavaScript - Interactive Features

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }

    // Search functionality with live suggestions
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    
    if (searchForm && searchInput && searchSuggestions) {
        // Handle form submission
        searchForm.addEventListener('submit', function(e) {
            if (!searchInput.value.trim()) {
                e.preventDefault();
                searchInput.focus();
            } else {
                searchSuggestions.style.display = 'none';
            }
        });
        
        // Live search suggestions
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetchSearchSuggestions(query);
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchForm.contains(e.target)) {
                searchSuggestions.style.display = 'none';
            }
        });
        
        // Show suggestions when input is focused
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                searchSuggestions.style.display = 'block';
            }
        });
    }
    
    // Fetch search suggestions
    function fetchSearchSuggestions(query) {
        fetch(`ajax/search_suggestions.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySearchSuggestions(data);
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                searchSuggestions.style.display = 'none';
            });
    }
    
    // Display search suggestions
    function displaySearchSuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            searchSuggestions.style.display = 'none';
            return;
        }
        
        searchSuggestions.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            
            const icon = suggestion.type === 'category' ? '📂' : '🛍️';
            item.innerHTML = `<span class="suggestion-icon">${icon}</span> ${suggestion.title}`;
            
            item.addEventListener('click', function() {
                if (suggestion.type === 'category') {
                    window.location.href = `products.php?kategori=${suggestion.id}`;
                } else {
                    searchInput.value = suggestion.title;
                    searchForm.submit();
                }
            });
            
            searchSuggestions.appendChild(item);
        });
        
        searchSuggestions.style.display = 'block';
    }

    // Email validation for STIS format
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !validateSTISEmail(this.value)) {
                this.setCustomValidity('Format email harus: [8 digit NIM]@stis.ac.id');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });

    // Wishlist functionality
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    });

    // Image slider/carousel
    initImageSliders();

    // File upload with drag & drop
    initFileUpload();

    // Auto-crop image preview
    initImagePreview();

    // Responsive image loading
    initLazyLoading();

    // Smooth scrolling
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Category filter
    const categoryFilter = document.querySelector('#category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            filterProductsByCategory(selectedCategory);
        });
    }

    // Price range filter
    const priceRangeInputs = document.querySelectorAll('.price-range input');
    priceRangeInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            filterProductsByPriceRange();
        }, 500));
    });

    // WhatsApp contact integration
    const whatsappBtns = document.querySelectorAll('.whatsapp-btn');
    whatsappBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const phoneNumber = this.dataset.phone;
            const message = this.dataset.message || 'Halo, saya tertarik dengan produk Anda di KosMarket';
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        });
    });
});

// Email validation function
function validateSTISEmail(email) {
    const regex = /^[0-9]{8}@stis\.ac\.id$/;
    return regex.test(email);
}

// Form validation function
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            showFieldError(input, 'Field ini wajib diisi');
        } else {
            hideFieldError(input);
        }

        // Email validation
        if (input.type === 'email' && input.value && !validateSTISEmail(input.value)) {
            isValid = false;
            showFieldError(input, 'Format email harus: [8 digit NIM]@stis.ac.id');
        }

        // Password validation
        if (input.type === 'password' && input.value && input.value.length < 6) {
            isValid = false;
            showFieldError(input, 'Password minimal 6 karakter');
        }

        // Confirm password validation
        if (input.name === 'confirm_password') {
            const passwordInput = form.querySelector('input[name="password"]');
            if (passwordInput && input.value !== passwordInput.value) {
                isValid = false;
                showFieldError(input, 'Password tidak cocok');
            }
        }
    });

    return isValid;
}

// Show field error
function showFieldError(input, message) {
    let errorElement = input.parentNode.querySelector('.field-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        input.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
    input.classList.add('error');
}

// Hide field error
function hideFieldError(input) {
    const errorElement = input.parentNode.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
    input.classList.remove('error');
}

// Wishlist toggle function
function toggleWishlist(productId, button) {
    if (!productId) return;

    const isActive = button.classList.contains('active');
    const action = isActive ? 'remove' : 'add';

    // Send AJAX request
    fetch('ajax/wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            product_id: productId,
            action: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('active');
            const icon = button.querySelector('i');
            if (button.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
            
            // Show notification
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan koneksi', 'error');
    });
}

// Image slider initialization
function initImageSliders() {
    const sliders = document.querySelectorAll('.image-slider');
    sliders.forEach(slider => {
        const images = slider.querySelectorAll('img');
        const indicators = slider.querySelectorAll('.indicator');
        const prevBtn = slider.querySelector('.prev-btn');
        const nextBtn = slider.querySelector('.next-btn');
        
        let currentIndex = 0;

        function showImage(index) {
            images.forEach((img, i) => {
                img.classList.toggle('active', i === index);
            });
            indicators.forEach((indicator, i) => {
                indicator.classList.toggle('active', i === index);
            });
        }

        function nextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            showImage(currentIndex);
        }

        function prevImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(currentIndex);
        }

        if (nextBtn) nextBtn.addEventListener('click', nextImage);
        if (prevBtn) prevBtn.addEventListener('click', prevImage);

        indicators.forEach((indicator, i) => {
            indicator.addEventListener('click', () => {
                currentIndex = i;
                showImage(currentIndex);
            });
        });

        // Auto-slide
        setInterval(nextImage, 5000);
    });
}

// File upload with drag & drop
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        const wrapper = input.closest('.file-upload-wrapper');
        if (!wrapper) return;

        const dropZone = wrapper.querySelector('.drop-zone');
        const fileList = wrapper.querySelector('.file-list');

        // Drag & drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        dropZone.addEventListener('drop', handleDrop, false);

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            dropZone.classList.add('highlight');
        }

        function unhighlight(e) {
            dropZone.classList.remove('highlight');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            input.files = files;
            displayFiles(files);
        }

        input.addEventListener('change', function() {
            displayFiles(this.files);
        });

        function displayFiles(files) {
            if (!fileList) return;
            
            fileList.innerHTML = '';
            [...files].forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                `;
                fileList.appendChild(fileItem);
            });
        }
    });
}

// Image preview with auto-crop
function initImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const files = this.files;
            const previewContainer = this.closest('.form-group').querySelector('.image-preview');
            
            if (!previewContainer) return;

            previewContainer.innerHTML = '';

            [...files].forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    });
}

// Lazy loading for images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        imageObserver.observe(img);
    });
}

// Filter products by category
function filterProductsByCategory(categoryId) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productCategory = product.dataset.category;
        if (!categoryId || productCategory === categoryId) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Filter products by price range
function filterProductsByPriceRange() {
    const minPrice = document.querySelector('#min-price').value;
    const maxPrice = document.querySelector('#max-price').value;
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productPrice = parseInt(product.dataset.price);
        const inRange = (!minPrice || productPrice >= minPrice) && 
                       (!maxPrice || productPrice <= maxPrice);
        
        product.style.display = inRange ? 'block' : 'none';
    });
}

// Utility functions
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

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification.success {
        background: #28a745;
    }
    
    .notification.error {
        background: #dc3545;
    }
    
    .notification.info {
        background: #17a2b8;
    }
    
    .notification.warning {
        background: #ffc107;
        color: #212529;
    }
    
    .field-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .form-control.error {
        border-color: #dc3545;
    }
    
    .drop-zone {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .drop-zone.highlight {
        border-color: var(--primary-peach);
        background: rgba(255, 205, 178, 0.1);
    }
    
    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
        border-radius: 4px;
    }
    
    .preview-image {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 0.5rem;
    }
    
    .image-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    img.lazy {
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    img.lazy.loaded {
        opacity: 1;
    }
`;
document.head.appendChild(style);
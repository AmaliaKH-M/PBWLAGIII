// KosMarket JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn")
  const mobileMenu = document.querySelector(".mobile-menu")

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener("click", () => {
      mobileMenu.classList.toggle("active")
    })
  }

  // --- START MODIFIKASI UNTUK LIVE SEARCH DAN SUGGESTION ---
  const searchInput = document.querySelector('.search-form input[name="search"]');
  const searchResultsContainer = document.createElement('div'); // Kontainer untuk hasil suggestion
  searchResultsContainer.className = 'search-suggestions'; // Tambahkan class untuk styling

  if (searchInput) {
    searchInput.parentElement.appendChild(searchResultsContainer); // Tambahkan kontainer ke DOM

    let timeout = null;

    searchInput.addEventListener('input', (e) => {
      clearTimeout(timeout); // Hapus timeout sebelumnya
      const query = e.target.value.trim();

      if (query.length > 2) { // Mulai pencarian setelah 2 karakter atau lebih
        timeout = setTimeout(() => {
          performLiveSearch(query, searchResultsContainer);
        }, 300); // Tunda 300ms untuk mengurangi jumlah request
      } else {
        searchResultsContainer.innerHTML = ''; // Kosongkan suggestion jika query terlalu pendek
        searchResultsContainer.style.display = 'none';
      }
    });

    // Sembunyikan suggestion saat klik di luar input atau hasil
    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !searchResultsContainer.contains(e.target)) {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.display = 'none';
      }
    });
  }

  // --- END MODIFIKASI UNTUK LIVE SEARCH DAN SUGGESTION ---

  // Search functionality (ini bisa dihapus atau dipertahankan jika Anda masih ingin validasi submit form)
  const searchForm = document.querySelector(".search-form")
  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      const currentSearchInput = this.querySelector('input[name="search"]')
      if (!currentSearchInput.value.trim()) {
        e.preventDefault()
        alert("Masukkan kata kunci pencarian")
      }
    })
  }


  // File upload preview
  const fileInputs = document.querySelectorAll('input[type="file"]')
  fileInputs.forEach((input) => {
    input.addEventListener("change", function () {
      previewFiles(this)
    })
  })

  // Drag and drop for file upload
  const fileUploadAreas = document.querySelectorAll(".file-upload")
  fileUploadAreas.forEach((area) => {
    area.addEventListener("dragover", function (e) {
      e.preventDefault()
      this.classList.add("dragover")
    })

    area.addEventListener("dragleave", function () {
      this.classList.remove("dragover")
    })

    area.addEventListener("drop", function (e) {
      e.preventDefault()
      this.classList.remove("dragover")

      const fileInput = this.querySelector('input[type="file"]')
      if (fileInput) {
        fileInput.files = e.dataTransfer.files
        previewFiles(fileInput)
      }
    })
  })

  // Image carousel
  initCarousels()

  // Wishlist functionality
  initWishlist()

  // Cart functionality
  initCart()

  // Form validation
  initFormValidation()

  // Auto-hide alerts
  setTimeout(() => {
    const alerts = document.querySelectorAll(".alert")
    alerts.forEach((alert) => {
      alert.style.opacity = "0"
      setTimeout(() => alert.remove(), 300)
    })
  }, 5000)
})

// --- START FUNGSI BARU UNTUK LIVE SEARCH ---
async function performLiveSearch(query, resultsContainer) {
  try {
    // Di sini Anda perlu memanggil API atau endpoint backend Anda
    // yang akan mengembalikan suggestion berdasarkan 'query'.
    // Contoh: 'ajax/search_suggestions.php'
    // Asumsi: Backend mengembalikan JSON array of strings atau objects
    // contoh: ["kos murah", "kos dekat kampus", "kos putri"]
    // atau: [{id:1, name:"Kos ABC"}, {id:2, name:"Kos XYZ"}]

    // Untuk tujuan demonstrasi, kita akan menggunakan data dummy
    // Anda harus mengganti bagian ini dengan panggilan fetch ke backend Anda
    // const response = await fetch(`ajax/search_suggestions.php?q=${encodeURIComponent(query)}`);
    // const data = await response.json();

    // Data dummy (ganti ini dengan hasil dari backend Anda)
    const allSuggestions = [
      "Kos Murah Jakarta",
      "Kos Dekat Kampus",
      "Kos Putri AC",
      "Kos Putra Surabaya",
      "Kos Bebas Bandung",
      "Kos Harian Jogja",
      "Kos Bulanan Bogor",
      "Kos Fasilitas Lengkap",
      "Kos Mewah Jakarta Selatan",
      "Kos Dekat Stasiun"
    ];

    const filteredSuggestions = allSuggestions.filter(suggestion =>
      suggestion.toLowerCase().includes(query.toLowerCase())
    );

    renderSuggestions(filteredSuggestions, resultsContainer);

  } catch (error) {
    console.error("Error fetching search suggestions:", error);
    resultsContainer.innerHTML = '';
    resultsContainer.style.display = 'none';
  }
}

function renderSuggestions(suggestions, container) {
  container.innerHTML = ''; // Kosongkan container sebelumnya
  if (suggestions.length > 0) {
    const ul = document.createElement('ul');
    suggestions.forEach(item => {
      const li = document.createElement('li');
      li.textContent = item;
      li.addEventListener('click', () => {
        document.querySelector('.search-form input[name="search"]').value = item;
        container.innerHTML = ''; // Sembunyikan suggestion setelah dipilih
        container.style.display = 'none';
        // Opsional: Langsung submit form pencarian setelah memilih suggestion
        // document.querySelector('.search-form').submit();
      });
      ul.appendChild(li);
    });
    container.appendChild(ul);
    container.style.display = 'block'; // Tampilkan container
  } else {
    container.innerHTML = '';
    container.style.display = 'none';
  }
}
// --- END FUNGSI BARU UNTUK LIVE SEARCH ---


// File preview function
function previewFiles(input) {
  const previewContainer =
    input.parentElement.querySelector(".file-preview") ||
    input.parentElement.parentElement.querySelector(".file-preview")

  if (!previewContainer) return

  previewContainer.innerHTML = ""

  if (input.files) {
    Array.from(input.files).forEach((file, index) => {
      if (file.type.startsWith("image/")) {
        const reader = new FileReader()
        reader.onload = (e) => {
          const previewItem = document.createElement("div")
          previewItem.className = "file-preview-item"
          previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}">
                        <button type="button" class="remove-btn" onclick="removePreview(this, ${index})">×</button>
                    `
          previewContainer.appendChild(previewItem)
        }
        reader.readAsDataURL(file)
      }
    })
  }
}

// Remove preview function
function removePreview(button, index) {
  const previewItem = button.parentElement
  const fileInput = previewItem.parentElement.parentElement.querySelector('input[type="file"]')

  // Create new FileList without the removed file
  const dt = new DataTransfer()
  const files = Array.from(fileInput.files)
  files.forEach((file, i) => {
    if (i !== index) {
      dt.items.add(file)
    }
  })
  fileInput.files = dt.files

  previewItem.remove()
}

// Initialize carousels
function initCarousels() {
  const carousels = document.querySelectorAll(".carousel")

  carousels.forEach((carousel) => {
    const items = carousel.querySelectorAll(".carousel-item")
    const indicators = carousel.querySelectorAll(".carousel-indicator")
    const prevBtn = carousel.querySelector(".carousel-prev")
    const nextBtn = carousel.querySelector(".carousel-next")

    let currentIndex = 0

    function showSlide(index) {
      items.forEach((item, i) => {
        item.classList.toggle("active", i === index)
      })

      indicators.forEach((indicator, i) => {
        indicator.classList.toggle("active", i === index)
      })
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % items.length
      showSlide(currentIndex)
    }

    function prevSlide() {
      currentIndex = (currentIndex - 1 + items.length) % items.length
      showSlide(currentIndex)
    }

    if (nextBtn) nextBtn.addEventListener("click", nextSlide)
    if (prevBtn) prevBtn.addEventListener("click", prevSlide)

    indicators.forEach((indicator, index) => {
      indicator.addEventListener("click", () => {
        currentIndex = index
        showSlide(currentIndex)
      })
    })

    // Auto-play carousel
    if (items.length > 1) {
      setInterval(nextSlide, 5000)
    }
  })
}

// Initialize wishlist functionality
function initWishlist() {
  const wishlistBtns = document.querySelectorAll(".wishlist-btn")

  wishlistBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault()
      e.stopPropagation()

      const productId = this.dataset.productId
      toggleWishlist(productId, this)
    })
  })
}

// Toggle wishlist
function toggleWishlist(productId, button) {
  fetch("ajax/wishlist.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `product_id=${productId}&action=toggle`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        button.classList.toggle("active")
        const icon = button.querySelector("i")
        if (data.action === "added") {
          icon.className = "fas fa-heart"
          showNotification("Ditambahkan ke wishlist", "success")
        } else {
          icon.className = "far fa-heart"
          showNotification("Dihapus dari wishlist", "info")
        }
      } else {
        if (data.message === "not_logged_in") {
          showNotification("Silakan login terlebih dahulu", "warning")
          setTimeout(() => {
            window.location.href = "login.php"
          }, 1500)
        } else {
          showNotification("Terjadi kesalahan", "danger")
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showNotification("Terjadi kesalahan", "danger")
    })
}

// Initialize cart functionality
function initCart() {
  const addToCartBtns = document.querySelectorAll(".add-to-cart-btn")
  const cartQuantityBtns = document.querySelectorAll(".cart-quantity-btn")

  addToCartBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault()

      const productId = this.dataset.productId
      addToCart(productId)
    })
  })

  cartQuantityBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const productId = this.dataset.productId
      const action = this.dataset.action
      updateCartQuantity(productId, action)
    })
  })
}

// Add to cart
function addToCart(productId) {
  fetch("ajax/cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `product_id=${productId}&action=add`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateCartBadge(data.cart_count)
        showNotification("Ditambahkan ke keranjang", "success")
      } else {
        if (data.message === "not_logged_in") {
          showNotification("Silakan login terlebih dahulu", "warning")
          setTimeout(() => {
            window.location.href = "login.php"
          }, 1500)
        } else {
          showNotification(data.message || "Terjadi kesalahan", "danger")
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showNotification("Terjadi kesalahan", "danger")
    })
}

// Update cart quantity
function updateCartQuantity(productId, action) {
  fetch("ajax/cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `product_id=${productId}&action=${action}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        location.reload() // Reload to update cart display
      } else {
        showNotification(data.message || "Terjadi kesalahan", "danger")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showNotification("Terjadi kesalahan", "danger")
    })
}

// Update cart badge
function updateCartBadge(count) {
  const badge = document.querySelector(".cart-badge .badge")
  if (badge) {
    badge.textContent = count
    badge.style.display = count > 0 ? "flex" : "none"
  }
}

// Initialize form validation
function initFormValidation() {
  const forms = document.querySelectorAll("form[data-validate]")

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this)) {
        e.preventDefault()
      }
    })
  })
}

// Validate form
function validateForm(form) {
  let isValid = true
  const requiredFields = form.querySelectorAll("[required]")

  requiredFields.forEach((field) => {
    if (!field.value.trim()) {
      showFieldError(field, "Field ini wajib diisi")
      isValid = false
    } else {
      clearFieldError(field)
    }
  })

  // Email validation
  const emailFields = form.querySelectorAll('input[type="email"]')
  emailFields.forEach((field) => {
    if (field.value && !isValidEmail(field.value)) {
      showFieldError(field, "Format email tidak valid")
      isValid = false
    }
  })

  // Password confirmation
  const passwordField = form.querySelector('input[name="password"]')
  const confirmPasswordField = form.querySelector('input[name="confirm_password"]')

  if (passwordField && confirmPasswordField) {
    if (passwordField.value !== confirmPasswordField.value) {
      showFieldError(confirmPasswordField, "Password tidak cocok")
      isValid = false
    }
  }

  return isValid
}

// Show field error
function showFieldError(field, message) {
  clearFieldError(field)

  field.classList.add("error")
  const errorDiv = document.createElement("div")
  errorDiv.className = "field-error"
  errorDiv.textContent = message
  field.parentElement.appendChild(errorDiv)
}

// Clear field error
function clearFieldError(field) {
  field.classList.remove("error")
  const existingError = field.parentElement.querySelector(".field-error")
  if (existingError) {
    existingError.remove()
  }
}

// Validate email
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

// Show notification
function showNotification(message, type = "info") {
  const notification = document.createElement("div")
  notification.className = `alert alert-${type} notification`
  notification.textContent = message
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `

  document.body.appendChild(notification)

  // Auto remove notification
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease"
    setTimeout(() => notification.remove(), 300)
  }, 3000)
}

// WhatsApp checkout
function checkoutWhatsApp() {
  const cartItems = document.querySelectorAll(".cart-item")
  if (cartItems.length === 0) {
    showNotification("Keranjang kosong", "warning")
    return
  }

  let message = "Halo! Saya ingin membeli barang berikut dari KosMarket:\n\n"
  let total = 0

  cartItems.forEach((item) => {
    const title = item.querySelector(".item-title").textContent
    const price = Number.parseInt(item.querySelector(".item-price").dataset.price)
    const quantity = Number.parseInt(item.querySelector(".quantity-input").value)
    const subtotal = price * quantity

    message += `• ${title} - ${formatRupiah(price)} x ${quantity} = ${formatRupiah(subtotal)}\n`
    total += subtotal
  })

  message += `\nTotal: ${formatRupiah(total)}\n\n`
  message += `Nama: ${document.querySelector("[data-user-name]")?.textContent || ""}\n`
  message += `Email: ${document.querySelector("[data-user-email]")?.textContent || ""}\n`
  message += `Lokasi: ${document.querySelector("[data-user-location]")?.textContent || ""}`

  const whatsappUrl = `https://wa.me/6281234567890?text=${encodeURIComponent(message)}`
  window.open(whatsappUrl, "_blank")
}

// Format rupiah
function formatRupiah(angka) {
  return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
}

// CSS animations
const style = document.createElement("style")
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .field-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .form-control.error {
        border-color: #dc3545;
    }
    
    .notification {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* --- START CSS BARU UNTUK LIVE SEARCH --- */
    .search-suggestions {
        position: absolute; /* Sesuaikan posisi agar di bawah input */
        width: 100%; /* Lebar sesuai input */
        background-color: #fff;
        border: 1px solid #ddd;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000; /* Pastikan di atas elemen lain */
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: none; /* Sembunyikan secara default */
    }

    .search-suggestions ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .search-suggestions li {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .search-suggestions li:last-child {
        border-bottom: none;
    }

    .search-suggestions li:hover {
        background-color: #f0f0f0;
    }
    /* --- END CSS BARU UNTUK LIVE SEARCH --- */
`
document.head.appendChild(style)
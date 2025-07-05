<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/kosmarket_db.php';
require_once 'config/helpers.php';
require_once 'classes/Cart.php';

$database = new Database();
$db = $database->getConnection();
$cart = new Cart($db);

// Get cart items
$cart_items = $cart->getUserCart($_SESSION['user_id']);
$cart_count = count($cart_items);

// Calculate total
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['harga'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - KosMarket</title>
    <meta name="description" content="Keranjang belanja Anda di KosMarket">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo logo-font">
                K<span class="heart">❤️</span>sMarket
            </a>

            <div class="search-box">
                <form action="products.php" method="GET" class="search-form">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" placeholder="Cari barang preloved..." autocomplete="off">
                </form>
            </div>

            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <li><a href="sell.php" class="btn btn-primary"><span class="icon">+</span> Jual/Donasi</a></li>
                <li><a href="wishlist.php"><span class="icon">♡</span></a></li>
                <li>
                    <a href="cart.php" class="cart-badge active">
                        <span class="icon">🛒</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="dashboard.php"><span class="icon">👤</span> Dashboard</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>

            <button class="mobile-menu-btn">
                <span class="hamburger">☰</span>
            </button>
        </div>

        <div class="mobile-menu">
            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <li><a href="sell.php"><span class="icon">+</span> Jual/Donasi</a></li>
                <li><a href="wishlist.php"><span class="icon">♡</span> Wishlist</a></li>
                <li><a href="cart.php"><span class="icon">🛒</span> Keranjang (<?= $cart_count ?>)</a></li>
                <li><a href="dashboard.php"><span class="icon">👤</span> Dashboard</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </div>
    </nav>

    <main class="container" style="margin-top: 2rem;">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span class="separator">></span>
            <span>Keranjang</span>
        </div>

        <div class="page-header">
            <h1>🛒 Keranjang Belanja</h1>
            <p class="text-muted">Barang yang siap untuk dibeli</p>
        </div>

        <div class="cart-content">
            <?php if (empty($cart_items)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <h3>Keranjang masih kosong</h3>
                    <p>Mulai tambahkan barang yang ingin dibeli!</p>
                    <a href="products.php" class="btn btn-primary">🛍️ Jelajahi Produk</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <div class="cart-header">
                            <h3>Barang dalam Keranjang (<?= count($cart_items) ?>)</h3>
                            <button onclick="clearCart()" class="btn btn-outline">🗑️ Kosongkan Keranjang</button>
                        </div>

                        <div class="cart-list">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item">
                                    <div class="item-image">
                                        <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.svg' ?>" 
                                             alt="<?= htmlspecialchars($item['judul']) ?>">
                                    </div>

                                    <div class="item-details">
                                        <h4 class="item-title"><?= htmlspecialchars($item['judul']) ?></h4>
                                        <div class="item-meta">
                                            <span class="badge badge-secondary"><?= $item['kondisi'] ?></span>
                                            <span class="item-seller">oleh <?= htmlspecialchars($item['nama_penjual']) ?></span>
                                        </div>
                                        <div class="item-location">
                                            📍 <?= htmlspecialchars($item['lokasi_penjual']) ?>
                                        </div>
                                    </div>

                                    <div class="item-quantity">
                                        <label>Jumlah:</label>
                                        <div class="quantity-controls">
                                            <button class="quantity-btn" onclick="updateQuantity(<?= $item['id_produk'] ?>, 'decrease')">-</button>
                                            <span class="quantity"><?= $item['quantity'] ?></span>
                                            <button class="quantity-btn" onclick="updateQuantity(<?= $item['id_produk'] ?>, 'increase')">+</button>
                                        </div>
                                    </div>

                                    <div class="item-price">
                                        <div class="unit-price"><?= formatRupiah($item['harga']) ?></div>
                                        <div class="total-price"><?= formatRupiah($item['harga'] * $item['quantity']) ?></div>
                                    </div>

                                    <div class="item-actions">
                                        <button onclick="removeFromCart(<?= $item['id_produk'] ?>)" class="btn btn-outline btn-sm">
                                            🗑️ Hapus
                                        </button>
                                        <a href="product.php?id=<?= $item['id_produk'] ?>" class="btn btn-primary btn-sm">
                                            👁 Lihat
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Ringkasan Pembelian</h3>
                            
                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Total Barang:</span>
                                    <span><?= array_sum(array_column($cart_items, 'quantity')) ?> item</span>
                                </div>
                                <div class="summary-row">
                                    <span>Total Harga:</span>
                                    <span class="total-amount"><?= formatRupiah($total_price) ?></span>
                                </div>
                            </div>

                            <div class="checkout-section">
                                <p class="checkout-note">
                                    💡 <strong>Cara Pembelian:</strong><br>
                                    Klik "Checkout via WhatsApp" untuk menghubungi penjual satu per satu dan melakukan transaksi langsung.
                                </p>
                                <button onclick="checkoutWhatsApp()" class="btn btn-primary w-100">
                                    💬 Checkout via WhatsApp
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="logo-font">K<span class="heart">❤️</span>sMarket</h3>
                    <p>Platform jual-beli dan donasi barang preloved khusus untuk komunitas mahasiswa dan penghuni kos.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Menu Utama</h3>
                    <ul>
                        <li><a href="products.php">Semua Produk</a></li>
                        <li><a href="sell.php">Jual/Donasi</a></li>
                        <li><a href="about.php">Tentang Kami</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Kontak</h3>
                    <ul>
                        <li><span class="contact-icon">📧</span> info@kosmarket.com</li>
                        <li><span class="contact-icon">📞</span> +62 812-3456-7890</li>
                        <li><span class="contact-icon">📍</span> Malang, Jawa Timur</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 KosMarket. Dibuat dengan <span style="color: #e74c3c;">❤️</span> untuk komunitas mahasiswa.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        function updateQuantity(productId, action) {
            fetch('ajax/cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `product_id=${productId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Gagal mengubah jumlah');
                }
            });
        }

        function removeFromCart(productId) {
            if (confirm('Yakin ingin menghapus barang ini dari keranjang?')) {
                fetch('ajax/cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `product_id=${productId}&action=remove`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal menghapus barang');
                    }
                });
            }
        }

        function clearCart() {
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                fetch('ajax/cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=clear'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal mengosongkan keranjang');
                    }
                });
            }
        }

        function checkoutWhatsApp() {
            // Script untuk checkout via WhatsApp akan mengirim pesan ke semua penjual
            alert('Fitur ini akan menghubungi setiap penjual via WhatsApp. Silakan konfirmasi pembelian langsung dengan masing-masing penjual.');
            
            // Implementasi checkout bisa ditambahkan di sini
            <?php foreach ($cart_items as $item): ?>
                const wa_link_<?= $item['id_produk'] ?> = "https://wa.me/<?= preg_replace('/[^0-9]/', '', $item['nomor_wa']) ?>?text=" + 
                    encodeURIComponent("Halo, saya tertarik membeli:\n<?= $item['judul'] ?>\nHarga: <?= formatRupiah($item['harga']) ?>\nJumlah: <?= $item['quantity'] ?>\n\nApakah masih tersedia?");
                window.open(wa_link_<?= $item['id_produk'] ?>, '_blank');
            <?php endforeach; ?>
        }
    </script>
</body>
</html>
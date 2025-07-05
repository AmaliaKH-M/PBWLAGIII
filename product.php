<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/kosmarket_db.php';
require_once 'config/helpers.php';
require_once 'classes/Product.php';

// Get product ID from URL
$product_id = $_GET['id'] ?? '';

if (!$product_id) {
    header('Location: products.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get product details
$item = $product->getById($product_id);

if (!$item) {
    header('Location: products.php');
    exit;
}

// Get related products
$related_products = $product->getByCategory($item['id_kategori'], 4, $product_id);

// Get cart count for logged in user
$cart_count = 0;
if (isLoggedIn()) {
    require_once 'classes/Cart.php';
    $cart = new Cart($db);
    $cart_count = $cart->getItemCount($_SESSION['user_id']);
}

// WhatsApp message template
$whatsapp_message = "Halo, saya tertarik dengan produk: " . $item['judul'] . "\n\n";
$whatsapp_message .= "Harga: " . ($item['tipe_barang'] === 'donasi' ? 'GRATIS' : formatRupiah($item['harga'])) . "\n";
$whatsapp_message .= "Kondisi: " . $item['kondisi'] . "\n\n";
$whatsapp_message .= "Apakah masih tersedia?";
$whatsapp_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $item['nomor_wa']) . "?text=" . urlencode($whatsapp_message);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['judul']) ?> - KosMarket</title>
    <meta name="description" content="<?= htmlspecialchars(substr($item['deskripsi'], 0, 150)) ?>...">
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
                <?php if (isLoggedIn()): ?>
                    <li><a href="sell.php" class="btn btn-primary"><span class="icon">+</span> Jual/Donasi</a></li>
                    <li><a href="wishlist.php"><span class="icon">♡</span></a></li>
                    <li>
                        <a href="cart.php" class="cart-badge">
                            <span class="icon">🛒</span>
                            <?php if ($cart_count > 0): ?>
                                <span class="badge"><?= $cart_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="dashboard.php"><span class="icon">👤</span> Dashboard</a></li>
                    <li><a href="logout.php">Keluar</a></li>
                <?php else: ?>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="register.php" class="btn btn-primary">Daftar</a></li>
                <?php endif; ?>
            </ul>

            <button class="mobile-menu-btn">
                <span class="hamburger">☰</span>
            </button>
        </div>

        <div class="mobile-menu">
            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="sell.php"><span class="icon">+</span> Jual/Donasi</a></li>
                    <li><a href="wishlist.php"><span class="icon">♡</span> Wishlist</a></li>
                    <li><a href="cart.php"><span class="icon">🛒</span> Keranjang (<?= $cart_count ?>)</a></li>
                    <li><a href="dashboard.php"><span class="icon">👤</span> Dashboard</a></li>
                    <li><a href="logout.php">Keluar</a></li>
                <?php else: ?>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="register.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container" style="margin-top: 2rem;">
        <div class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span class="separator">></span>
            <a href="products.php">Produk</a>
            <span class="separator">></span>
            <span><?= htmlspecialchars($item['judul']) ?></span>
        </div>

        <div class="product-detail">
            <div class="product-images">
                <div class="main-image">
                    <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.svg' ?>" 
                         alt="<?= htmlspecialchars($item['judul']) ?>" id="main-product-image">
                    
                    <div class="ribbon <?= $item['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                        <span><?= $item['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                    </div>
                </div>

                <?php if ($item['foto2'] || $item['foto3']): ?>
                    <div class="image-thumbnails">
                        <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.svg' ?>" 
                             alt="Foto 1" class="thumbnail active" onclick="changeMainImage(this)">
                        
                        <?php if ($item['foto2']): ?>
                            <img src="uploads/produk/<?= $item['foto2'] ?>" 
                                 alt="Foto 2" class="thumbnail" onclick="changeMainImage(this)">
                        <?php endif; ?>
                        
                        <?php if ($item['foto3']): ?>
                            <img src="uploads/produk/<?= $item['foto3'] ?>" 
                                 alt="Foto 3" class="thumbnail" onclick="changeMainImage(this)">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h1><?= htmlspecialchars($item['judul']) ?></h1>
                
                <div class="product-meta">
                    <span class="badge badge-primary"><?= getCategoryEmoji($item['nama_kategori']) ?> <?= $item['nama_kategori'] ?></span>
                    <span class="badge badge-secondary"><?= $item['kondisi'] ?></span>
                </div>

                <div class="product-price">
                    <?php if ($item['tipe_barang'] === 'jual'): ?>
                        <span class="current-price"><?= formatRupiah($item['harga']) ?></span>
                        <?php if ($item['harga_asli']): ?>
                            <span class="original-price"><?= formatRupiah($item['harga_asli']) ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="free-price">GRATIS</span>
                    <?php endif; ?>
                </div>

                <div class="product-description">
                    <h3>Deskripsi</h3>
                    <p><?= nl2br(htmlspecialchars($item['deskripsi'])) ?></p>
                </div>

                <div class="product-details">
                    <div class="detail-item">
                        <span class="label">Penjual:</span>
                        <span class="value"><?= htmlspecialchars($item['nama_penjual']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Lokasi:</span>
                        <span class="value">📍 <?= htmlspecialchars($item['lokasi_penjual']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">WhatsApp:</span>
                        <span class="value">📞 <?= htmlspecialchars($item['nomor_wa']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Diposting:</span>
                        <span class="value">⏰ <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></span>
                    </div>
                </div>

                <div class="product-actions">
                    <?php if (isLoggedIn()): ?>
                        <button class="wishlist-btn" data-product-id="<?= $item['id_produk'] ?>">
                            <span class="heart">♡</span> Wishlist
                        </button>
                        
                        <?php if ($item['tipe_barang'] === 'jual'): ?>
                            <button class="add-to-cart-btn" data-product-id="<?= $item['id_produk'] ?>">
                                🛒 Tambah ke Keranjang
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <a href="<?= $whatsapp_link ?>" target="_blank" class="btn btn-primary btn-large">
                        💬 Hubungi Penjual
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($related_products)): ?>
            <section class="related-products">
                <h2>Produk Serupa</h2>
                <div class="product-grid">
                    <?php foreach ($related_products as $related): ?>
                        <div class="card">
                            <div style="position: relative;">
                                                            <img src="<?= $related['foto1'] ? 'uploads/produk/' . $related['foto1'] : 'assets/images/no-image.svg' ?>" 
                                 alt="<?= htmlspecialchars($related['judul']) ?>" class="card-img">
                                
                                <div class="ribbon <?= $related['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                                    <span><?= $related['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                                </div>

                                <?php if (isLoggedIn()): ?>
                                    <button class="wishlist-btn" data-product-id="<?= $related['id_produk'] ?>">
                                        <span class="heart">♡</span>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <h3 class="card-title"><?= htmlspecialchars($related['judul']) ?></h3>
                                <div class="badge badge-secondary mb-2"><?= $related['kondisi'] ?></div>
                                
                                <?php if ($related['tipe_barang'] === 'jual'): ?>
                                    <div class="card-price"><?= formatRupiah($related['harga']) ?></div>
                                <?php else: ?>
                                    <div class="card-price free">GRATIS</div>
                                <?php endif; ?>

                                <div class="card-location">
                                    <span class="location-icon">📍</span>
                                    <span><?= htmlspecialchars($related['lokasi_penjual']) ?></span>
                                </div>

                                <div class="card-footer">
                                    <span class="card-seller">oleh <?= htmlspecialchars($related['nama_penjual']) ?></span>
                                    <a href="product.php?id=<?= $related['id_produk'] ?>" class="btn btn-primary">
                                        <span class="view-icon">👁</span> Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
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
        function changeMainImage(thumbnail) {
            const mainImage = document.getElementById('main-product-image');
            mainImage.src = thumbnail.src;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }
    </script>
</body>
</html>
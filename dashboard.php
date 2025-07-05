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
require_once 'classes/User.php';
require_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$product = new Product($db);

// Get user data
$user_data = $user->getUserById($_SESSION['user_id']);
$user_stats = $user->getUserStats($_SESSION['user_id']);
$user_products = $product->getAll(10, null, null, null, null, $_SESSION['user_id']);

// Get cart count
$cart_count = 0;
if (isLoggedIn()) {
    require_once 'classes/Cart.php';
    $cart = new Cart($db);
    $cart_count = $cart->getItemCount($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KosMarket</title>
    <meta name="description" content="Dashboard pengguna KosMarket">
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
                    <a href="cart.php" class="cart-badge">
                        <span class="icon">🛒</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="dashboard.php" class="active"><span class="icon">👤</span> Dashboard</a></li>
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
            <span>Dashboard</span>
        </div>

        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p class="text-muted">Selamat datang, <?= htmlspecialchars($user_data['nama']) ?>!</p>
        </div>

        <div class="dashboard-grid">
            <!-- Profile Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>👤 Profil Saya</h3>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="info-item">
                            <span class="label">Nama:</span>
                            <span class="value"><?= htmlspecialchars($user_data['nama']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Email:</span>
                            <span class="value"><?= htmlspecialchars($user_data['email']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">WhatsApp:</span>
                            <span class="value"><?= htmlspecialchars($user_data['nomor_wa']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Alamat Kos:</span>
                            <span class="value"><?= htmlspecialchars($user_data['lokasi_kos']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Bergabung:</span>
                            <span class="value"><?= date('d/m/Y', strtotime($user_data['created_at'])) ?></span>
                        </div>
                    </div>
                    <a href="profile.php" class="btn btn-outline">✏️ Edit Profil</a>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>📊 Statistik Produk</h3>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?= $user_stats['total_produk'] ?></div>
                            <div class="stat-label">Total Produk</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $user_stats['tersedia'] ?></div>
                            <div class="stat-label">Tersedia</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $user_stats['terjual'] ?></div>
                            <div class="stat-label">Terjual</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= $user_stats['terdonasi'] ?></div>
                            <div class="stat-label">Terdonasi</div>
                        </div>
                    </div>
                    <a href="sell.php" class="btn btn-primary">📤 Jual/Donasi Barang</a>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>📦 Produk Saya Terbaru</h2>
                <a href="my-products.php" class="btn btn-outline">Lihat Semua</a>
            </div>

            <?php if (empty($user_products)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h3>Belum ada produk</h3>
                    <p>Mulai jual atau donasi barang pertama Anda!</p>
                    <a href="sell.php" class="btn btn-primary">📤 Jual/Donasi Sekarang</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach (array_slice($user_products, 0, 6) as $item): ?>
                        <div class="card">
                            <div style="position: relative;">
                                <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.svg' ?>" 
                                     alt="<?= htmlspecialchars($item['judul']) ?>" class="card-img">
                                
                                <div class="ribbon <?= $item['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                                    <span><?= $item['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                                </div>

                                <div class="status-badge status-<?= $item['status'] ?>">
                                    <?= ucfirst($item['status']) ?>
                                </div>
                            </div>

                            <div class="card-body">
                                <h3 class="card-title"><?= htmlspecialchars($item['judul']) ?></h3>
                                <div class="badge badge-secondary mb-2"><?= $item['kondisi'] ?></div>
                                
                                <?php if ($item['tipe_barang'] === 'jual'): ?>
                                    <div class="card-price"><?= formatRupiah($item['harga']) ?></div>
                                <?php else: ?>
                                    <div class="card-price free">GRATIS</div>
                                <?php endif; ?>

                                <div class="card-footer">
                                    <span class="card-date">📅 <?= date('d/m/Y', strtotime($item['created_at'])) ?></span>
                                    <div class="product-actions">
                                        <a href="product.php?id=<?= $item['id_produk'] ?>" class="btn btn-outline btn-sm">
                                            👁 Lihat
                                        </a>
                                        <a href="edit-product.php?id=<?= $item['id_produk'] ?>" class="btn btn-primary btn-sm">
                                            ✏️ Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
</body>
</html>
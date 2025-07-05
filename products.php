<?php
session_start();
require_once 'config/kosmarket_db.php';
require_once 'config/helpers.php';
require_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get filter parameters
$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$tipe = $_GET['tipe'] ?? '';
$kondisi = $_GET['kondisi'] ?? '';

// Get all products with filters
$products = $product->getAll(50, $search, $kategori, $tipe, $kondisi);
$categories = $product->getCategories();

// Get cart count for logged in user
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
    <title>Semua Produk - KosMarket</title>
    <meta name="description" content="Browse semua produk preloved dari komunitas STIS">
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
                    <input type="text" name="search" placeholder="Cari barang preloved..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                </form>
            </div>

            <ul class="nav-menu">
                <li><a href="products.php" class="active">Semua Produk</a></li>
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
            <span>Semua Produk</span>
        </div>

        <div class="d-flex justify-between align-center mb-4">
            <div>
                <h1>Semua Produk</h1>
                <p class="text-muted">Ditemukan <?= count($products) ?> produk</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section mb-4">
            <form action="products.php" method="GET" class="filter-form">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                
                <div class="filter-group">
                    <label>Kategori:</label>
                    <select name="kategori" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id_kategori'] ?>" <?= $kategori == $cat['id_kategori'] ? 'selected' : '' ?>>
                                <?= getCategoryEmoji($cat['nama_kategori']) ?> <?= $cat['nama_kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Tipe:</label>
                    <select name="tipe" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="jual" <?= $tipe == 'jual' ? 'selected' : '' ?>>Dijual</option>
                        <option value="donasi" <?= $tipe == 'donasi' ? 'selected' : '' ?>>Gratis/Donasi</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Kondisi:</label>
                    <select name="kondisi" onchange="this.form.submit()">
                        <option value="">Semua Kondisi</option>
                        <option value="Baru" <?= $kondisi == 'Baru' ? 'selected' : '' ?>>Baru</option>
                        <option value="Sangat Baik" <?= $kondisi == 'Sangat Baik' ? 'selected' : '' ?>>Sangat Baik</option>
                        <option value="Baik" <?= $kondisi == 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Cukup" <?= $kondisi == 'Cukup' ? 'selected' : '' ?>>Cukup</option>
                    </select>
                </div>

                <button type="button" onclick="window.location.href='products.php'" class="btn btn-outline">Reset</button>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h3>Tidak ada produk ditemukan</h3>
                    <p>Coba ubah filter pencarian Anda</p>
                    <a href="products.php" class="btn btn-primary">Lihat Semua Produk</a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $item): ?>
                    <div class="card">
                        <div style="position: relative;">
                                                     <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.svg' ?>" 
                              alt="<?= htmlspecialchars($item['judul']) ?>" class="card-img">
                            
                            <div class="ribbon <?= $item['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                                <span><?= $item['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                            </div>

                            <?php if (isLoggedIn()): ?>
                                <button class="wishlist-btn" data-product-id="<?= $item['id_produk'] ?>">
                                    <span class="heart">♡</span>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($item['judul']) ?></h3>
                            <div class="badge badge-secondary mb-2"><?= $item['kondisi'] ?></div>
                            
                            <?php if ($item['tipe_barang'] === 'jual'): ?>
                                <div class="card-price"><?= formatRupiah($item['harga']) ?></div>
                                <?php if ($item['harga_asli']): ?>
                                    <div class="text-muted" style="text-decoration: line-through; font-size: 0.9rem;">
                                        <?= formatRupiah($item['harga_asli']) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="card-price free">GRATIS</div>
                            <?php endif; ?>

                            <div class="card-location">
                                <span class="location-icon">📍</span>
                                <span><?= htmlspecialchars($item['lokasi_penjual']) ?></span>
                            </div>

                            <div class="card-footer">
                                <span class="card-seller">oleh <?= htmlspecialchars($item['nama_penjual']) ?></span>
                                <a href="product.php?id=<?= $item['id_produk'] ?>" class="btn btn-primary">
                                    <span class="view-icon">👁</span> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                    <h3>Kategori</h3>
                    <ul>
                        <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                            <li><a href="products.php?kategori=<?= $category['id_kategori'] ?>"><?= $category['nama_kategori'] ?></a></li>
                        <?php endforeach; ?>
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

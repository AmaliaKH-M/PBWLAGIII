<?php
require_once 'config/kosmarket_db.php';
require_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get featured products
$featured_products = $product->getAll(8);
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
    <title>KosMarket - Jual Beli Barang Kosan Preloved</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo logo-font">
                K<span class="heart">❤️</span>sMarket
            </a>

            <div class="search-box">
                <form action="products.php" method="GET" class="search-form">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Cari barang preloved..." value="<?= $_GET['search'] ?? '' ?>">
                </form>
            </div>

            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="sell.php" class="btn btn-primary"><i class="fas fa-plus"></i> Jual/Donasi</a></li>
                    <li><a href="wishlist.php"><i class="far fa-heart"></i></a></li>
                    <li>
                        <a href="cart.php" class="cart-badge">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="badge"><?= $cart_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                    <li><a href="logout.php">Keluar</a></li>
                <?php else: ?>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="register.php" class="btn btn-primary">Daftar</a></li>
                <?php endif; ?>
            </ul>

            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="mobile-menu">
            <ul class="nav-menu">
                <li><a href="products.php">Semua Produk</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="sell.php"><i class="fas fa-plus"></i> Jual/Donasi</a></li>
                    <li><a href="wishlist.php"><i class="far fa-heart"></i> Wishlist</a></li>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Keranjang (<?= $cart_count ?>)</a></li>
                    <li><a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
                    <li><a href="logout.php">Keluar</a></li>
                <?php else: ?>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="register.php">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main>
        <section class="hero" style="background-image: url('assets/Background.jpg');">
            <div class="hero-overlay">
                <div class="container hero-content">
                    <div class="hero-buttons hero-buttons-bottom">
                        </div>
                </div>
            </div>
        </section>

        <section class="categories">
            <div class="container">
                <h2 class="text-center mb-4">Kategori Populer</h2>
                <p class="text-center text-muted mb-5">Temukan barang preloved berkualitas dari berbagai kategori</p>
                
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <a href="products.php?kategori=<?= $category['id_kategori'] ?>" class="category-card">
                            <div class="category-icon" style="background: <?= $category['color'] ?>"> <i class="fas fa-<?= $category['icon'] ?>"></i>
                            </div>
                            <h3><?= $category['nama_kategori'] ?></h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="container">
            <div class="d-flex justify-between align-center mb-4">
                <div>
                    <h2>Barang Pilihan</h2>
                    <p class="text-muted">Barang preloved terbaik dari komunitas mahasiswa</p>
                </div>
                <a href="products.php" class="btn btn-outline">Lihat Semua</a>
            </div>

            <div class="product-grid">
                <?php foreach ($featured_products as $item): ?>
                    <div class="card">
                        <div style="position: relative;">
                            <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.jpg' ?>" 
                                 alt="<?= htmlspecialchars($item['judul']) ?>" class="card-img">
                            
                            <div class="ribbon <?= $item['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                                <span><?= $item['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                            </div>

                            <?php if (isLoggedIn()): ?>
                                <button class="wishlist-btn" data-product-id="<?= $item['id_produk'] ?>">
                                    <i class="far fa-heart"></i>
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
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($item['lokasi_penjual']) ?></span>
                            </div>

                            <div class="card-footer">
                                <span class="card-seller">oleh <?= htmlspecialchars($item['nama_penjual']) ?></span>
                                <a href="product.php?id=<?= $item['id_produk'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="categories">
            <div class="container">
                <h2 class="text-center mb-4">Cara Kerja KosMarket</h2>
                <p class="text-center text-muted mb-5">Mudah dan simpel! Ikuti 4 langkah berikut</p>
                
                <div class="categories-grid">
                    <div class="category-card">
                        <div class="category-icon" style="background: var(--primary-peach)">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3>Daftar Akun</h3>
                        <p>Buat akun dengan email dan lengkapi profil kamu</p>
                    </div>
                    
                    <div class="category-card">
                        <div class="category-icon" style="background: var(--secondary-peach)">
                            <i class="fas fa-upload"></i>
                        </div>
                        <h3>Upload Barang</h3>
                        <p>Foto barangmu dan pilih mau dijual atau didonasi</p>
                    </div>
                    
                    <div class="category-card">
                        <div class="category-icon" style="background: var(--tertiary-rose)">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Cari & Temukan</h3>
                        <p>Browse barang yang kamu butuhkan dari berbagai kategori</p>
                    </div>
                    
                    <div class="category-card">
                        <div class="category-icon" style="background: var(--quaternary-mauve)">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h3>Hubungi Penjual</h3>
                        <p>Chat langsung via WhatsApp untuk nego dan transaksi</p>
                    </div>
                </div>
            </div>
        </section>
    </main> <footer class="footer">
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
                        <li><i class="fas fa-envelope"></i> info@kosmarket.com</li>
                        <li><i class="fas fa-phone"></i> +62 812-3456-7890</li>
                        <li><i class="fas fa-map-marker-alt"></i> Malang, Jawa Timur</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 KosMarket. Dibuat dengan <i class="fas fa-heart" style="color: #e74c3c;"></i> untuk komunitas mahasiswa.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
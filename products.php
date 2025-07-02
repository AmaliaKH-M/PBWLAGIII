<?php
require_once 'config/kosmarket_db.php';
require_once 'classes/Product.php';
require_once 'classes/Cart.php';
require_once 'classes/Wishlist.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get filter parameters
$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$tipe = $_GET['tipe'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

// Get products with filters
$products = $product->getAll(null, $kategori, $tipe, $search);
$categories = $product->getCategories();

// Get cart count for logged in user
$cart_count = 0;
if (isLoggedIn()) {
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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo logo-font">
                K<span class="heart">❤️</span>sMarket
            </a>

            <div class="search-box">
                <form action="products.php" method="GET" class="search-form">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Cari barang preloved..." value="<?= htmlspecialchars($search) ?>">
                    <?php if ($kategori): ?>
                        <input type="hidden" name="kategori" value="<?= $kategori ?>">
                    <?php endif; ?>
                    <?php if ($tipe): ?>
                        <input type="hidden" name="tipe" value="<?= $tipe ?>">
                    <?php endif; ?>
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
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <!-- Header -->
        <div class="mb-4">
            <h1>Semua Produk</h1>
            <p class="text-muted">Temukan barang preloved berkualitas dari komunitas mahasiswa</p>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="d-flex" style="gap: 1rem; flex-wrap: wrap; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-control form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_kategori'] ?>" <?= $kategori == $cat['id_kategori'] ? 'selected' : '' ?>>
                                    <?= $cat['nama_kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-control form-select">
                            <option value="">Semua Tipe</option>
                            <option value="jual" <?= $tipe === 'jual' ? 'selected' : '' ?>>Dijual</option>
                            <option value="donasi" <?= $tipe === 'donasi' ? 'selected' : '' ?>>Donasi</option>
                        </select>
                    </div>

                    <?php if ($search): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    
                    <a href="products.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </form>
            </div>
        </div>

        <!-- Results -->
        <div class="mb-4">
            <p class="text-muted">
                Menampilkan <?= count($products) ?> produk
                <?php if ($search): ?>
                    untuk pencarian "<?= htmlspecialchars($search) ?>"
                <?php endif; ?>
            </p>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="text-center" style="padding: 3rem 0;">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                <h3>Tidak ada produk ditemukan</h3>
                <p class="text-muted">Coba ubah filter pencarian atau kata kunci</p>
                <a href="products.php" class="btn btn-primary">Lihat Semua Produk</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $item): ?>
                    <div class="card">
                        <div style="position: relative;">
                            <img src="<?= $item['foto1'] ? 'uploads/produk/' . $item['foto1'] : 'assets/images/no-image.jpg' ?>" 
                                 alt="<?= htmlspecialchars($item['judul']) ?>" class="card-img">
                            
                            <!-- Corner Ribbon -->
                            <div class="ribbon <?= $item['tipe_barang'] === 'donasi' ? 'free' : '' ?>">
                                <span><?= $item['tipe_barang'] === 'donasi' ? 'GRATIS' : 'DIJUAL' ?></span>
                            </div>

                            <!-- Wishlist Button -->
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
        <?php endif; ?>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

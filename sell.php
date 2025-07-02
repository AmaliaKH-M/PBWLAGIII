<?php
require_once 'config/kosmarket_db.php';
require_once 'classes/Product.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$error = '';
$success = '';

if ($_POST) {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = $_POST['harga'] ?? 0;
    $harga_asli = $_POST['harga_asli'] ?? null;
    $kondisi = $_POST['kondisi'];
    $tipe_barang = $_POST['tipe_barang'];
    $id_kategori = $_POST['id_kategori'];
    
    // Validation
    if (empty($judul) || empty($deskripsi) || empty($kondisi) || empty($tipe_barang) || empty($id_kategori)) {
        $error = 'Semua field wajib diisi';
    } else {
        // Handle file uploads
        $foto1 = $foto2 = $foto3 = null;
        
        if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === 0) {
            $foto1 = uploadImage($_FILES['foto1']);
        }
        if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] === 0) {
            $foto2 = uploadImage($_FILES['foto2']);
        }
        if (isset($_FILES['foto3']) && $_FILES['foto3']['error'] === 0) {
            $foto3 = uploadImage($_FILES['foto3']);
        }
        
        $data = [
            'id_user' => $_SESSION['user_id'],
            'id_kategori' => $id_kategori,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'harga' => $tipe_barang === 'donasi' ? 0 : $harga,
            'harga_asli' => $harga_asli,
            'kondisi' => $kondisi,
            'tipe_barang' => $tipe_barang,
            'foto1' => $foto1,
            'foto2' => $foto2,
            'foto3' => $foto3
        ];
        
        if ($product->create($data)) {
            $success = 'Produk berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan produk';
        }
    }
}

$categories = $product->getCategories();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jual/Donasi Barang - KosMarket</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 3rem auto; padding: 2rem;">
        <div class="card">
            <div class="card-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <h1><i class="fas fa-plus-circle"></i> Jual/Donasi Barang</h1>
                    <p class="text-muted">Bagikan barang preloved kamu dengan komunitas</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" data-validate>
                    <div class="form-group">
                        <label class="form-label">Judul Barang *</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Laptop ASUS VivoBook 14" required value="<?= $_POST['judul'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi *</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan kondisi, spesifikasi, dan detail lainnya..." required><?= $_POST['deskripsi'] ?? '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori *</label>
                        <select name="id_kategori" class="form-control form-select" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id_kategori'] ?>" <?= ($_POST['id_kategori'] ?? '') == $category['id_kategori'] ? 'selected' : '' ?>>
                                    <?= $category['nama_kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kondisi Barang *</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem;">
                            <div class="form-check">
                                <input type="radio" name="kondisi" value="Baru" id="kondisi_baru" <?= ($_POST['kondisi'] ?? '') === 'Baru' ? 'checked' : '' ?>>
                                <label for="kondisi_baru">Baru</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="kondisi" value="Sangat Baik" id="kondisi_sangat_baik" <?= ($_POST['kondisi'] ?? '') === 'Sangat Baik' ? 'checked' : '' ?>>
                                <label for="kondisi_sangat_baik">Sangat Baik</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="kondisi" value="Baik" id="kondisi_baik" <?= ($_POST['kondisi'] ?? '') === 'Baik' ? 'checked' : '' ?>>
                                <label for="kondisi_baik">Baik</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="kondisi" value="Cukup" id="kondisi_cukup" <?= ($_POST['kondisi'] ?? '') === 'Cukup' ? 'checked' : '' ?>>
                                <label for="kondisi_cukup">Cukup</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe *</label>
                        <div style="display: flex; gap: 2rem;">
                            <div class="form-check">
                                <input type="radio" name="tipe_barang" value="jual" id="tipe_jual" <?= ($_POST['tipe_barang'] ?? '') === 'jual' ? 'checked' : '' ?>>
                                <label for="tipe_jual">Dijual</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="tipe_barang" value="donasi" id="tipe_donasi" <?= ($_POST['tipe_barang'] ?? '') === 'donasi' ? 'checked' : '' ?>>
                                <label for="tipe_donasi">Donasi (Gratis)</label>
                            </div>
                        </div>
                    </div>

                    <div id="price-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="0" value="<?= $_POST['harga'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Asli (Opsional)</label>
                            <input type="number" name="harga_asli" class="form-control" placeholder="Harga beli dulu" value="<?= $_POST['harga_asli'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Barang</label>
                        <div class="file-upload">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                            <p>Klik atau drag & drop foto di sini</p>
                            <p class="text-muted" style="font-size: 0.9rem;">Maksimal 3 foto, ukuran maks 2MB per foto</p>
                            <input type="file" name="foto1" accept="image/*" style="display: none;">
                        </div>
                        <div class="file-preview"></div>
                        
                        <div style="margin-top: 1rem;">
                            <input type="file" name="foto2" accept="image/*" style="margin-bottom: 0.5rem;">
                            <input type="file" name="foto3" accept="image/*">
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Produk
                        </button>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        // Show/hide price fields based on type
        document.addEventListener('DOMContentLoaded', function() {
            const tipeRadios = document.querySelectorAll('input[name="tipe_barang"]');
            const priceFields = document.getElementById('price-fields');
            
            function togglePriceFields() {
                const selectedTipe = document.querySelector('input[name="tipe_barang"]:checked');
                if (selectedTipe && selectedTipe.value === 'jual') {
                    priceFields.style.display = 'block';
                } else {
                    priceFields.style.display = 'none';
                }
            }
            
            tipeRadios.forEach(radio => {
                radio.addEventListener('change', togglePriceFields);
            });
            
            // Initial check
            togglePriceFields();
        });
    </script>
</body>
</html>

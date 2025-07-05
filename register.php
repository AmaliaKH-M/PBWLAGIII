<?php
session_start();
require_once 'config/kosmarket_db.php';
require_once 'config/helpers.php';
require_once 'classes/User.php';

$error = '';
$success = '';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nomor_wa = trim($_POST['nomor_wa']);
    $lokasi_kos = trim($_POST['lokasi_kos']);
    
    // Validation
    if (empty($nama) || empty($email) || empty($password) || empty($nomor_wa) || empty($lokasi_kos)) {
        $error = 'Semua field harus diisi';
    } elseif (!validateSTISEmail($email)) {
        $error = 'Email harus menggunakan format [9 digit NIM]@stis.ac.id';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        $user_id = $user->register($nama, $email, $password, $nomor_wa, $lokasi_kos);
        
        if ($user_id) {
            // Auto-login after registration
            $user_data = $user->getUserById($user_id);
            $_SESSION['user_id'] = $user_data['id_user'];
            $_SESSION['nama'] = $user_data['nama'];
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['role'] = $user_data['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email sudah terdaftar atau terjadi kesalahan';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KosMarket</title>
    <meta name="description" content="Daftar akun KosMarket dengan email STIS untuk bergabung dengan komunitas">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin: 3rem auto; padding: 2rem;">
        <div class="card">
            <div class="card-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <h1 class="logo logo-font" style="font-size: 2.5rem; margin-bottom: 1rem;">
                        K<span class="heart">❤️</span>sMarket
                    </h1>
                    <h2>Buat Akun Baru</h2>
                    <p class="text-muted">Bergabung dengan komunitas mahasiswa KosMarket</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required value="<?= $_POST['nama'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email STIS</label>
                        <input type="email" name="email" class="form-control" placeholder="222312964@stis.ac.id" required value="<?= $_POST['email'] ?? '' ?>">
                        <small class="form-text text-muted">Gunakan format: [9 digit NIM]@stis.ac.id</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="tel" name="nomor_wa" class="form-control" placeholder="+62 812-3456-7890" required value="<?= $_POST['nomor_wa'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Kos Lengkap</label>
                        <input type="text" name="lokasi_kos" class="form-control" placeholder="Contoh: Kos Melati, Jl. Otto Iskandardinata No.12, Jakarta Timur" required value="<?= $_POST['lokasi_kos'] ?? '' ?>">
                        <small class="form-text text-muted">Tulis alamat kos lengkap Anda</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <span class="register-icon">📝</span> Daftar
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-muted">
                        Sudah punya akun? 
                        <a href="login.php" style="color: var(--quaternary-mauve); font-weight: 500;">Masuk di sini</a>
                    </p>
                    <a href="index.php" class="text-muted">← Kembali ke beranda</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

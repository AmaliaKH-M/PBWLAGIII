<?php
session_start();
require_once 'config/kosmarket_db.php';
require_once 'classes/User.php';

$error = '';
$success = '';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        $user_data = $user->login($email, $password);
        
        if ($user_data) {
            $_SESSION['user_id'] = $user_data['id_user'];
            $_SESSION['nama'] = $user_data['nama'];
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['role'] = $user_data['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email atau password salah';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KosMarket</title>
    <meta name="description" content="Masuk ke akun KosMarket untuk akses fitur jual-beli barang preloved">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin: 5rem auto; padding: 2rem;">
        <div class="card">
            <div class="card-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <h1 class="logo logo-font" style="font-size: 2.5rem; margin-bottom: 1rem;">
                        K<span class="heart">❤️</span>sMarket
                    </h1>
                    <h2>Masuk ke Akun</h2>
                    <p class="text-muted">Masuk untuk mulai jual-beli barang preloved</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= $_POST['email'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <span class="login-icon">🔑</span> Masuk
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-muted">
                        Belum punya akun? 
                        <a href="register.php" style="color: var(--quaternary-mauve); font-weight: 500;">Daftar sekarang</a>
                    </p>
                    <a href="index.php" class="text-muted">← Kembali ke beranda</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

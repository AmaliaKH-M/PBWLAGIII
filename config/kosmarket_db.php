<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'kosmarket_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

// Session management
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function generateAngkatan($email) {
    // Extract angkatan from email format: 12345678@stis.ac.id
    if (preg_match('/^(\d{8})@stis\.ac\.id$/', $email, $matches)) {
        return substr($matches[1], 0, 2);
    }
    return '00';
}

function validateSTISEmail($email) {
    return preg_match('/^[0-9]{8}@stis\.ac\.id$/', $email);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'baru saja';
    if ($time < 3600) return floor($time/60) . ' menit lalu';
    if ($time < 86400) return floor($time/3600) . ' jam lalu';
    if ($time < 2592000) return floor($time/86400) . ' hari lalu';
    if ($time < 31536000) return floor($time/2592000) . ' bulan lalu';
    return floor($time/31536000) . ' tahun lalu';
}
?>
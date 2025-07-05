<?php
class Wishlist {
    private $conn;
    private $table = 'wishlist';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addItem($user_id, $product_id) {
        // Check if item already exists in wishlist
        $stmt = $this->conn->prepare("SELECT id_wishlist FROM " . $this->table . " WHERE id_user = ? AND id_produk = ?");
        $stmt->execute([$user_id, $product_id]);
        
        if ($stmt->rowCount() > 0) {
            return false; // Already in wishlist
        }
        
        // Insert new item
        $sql = "INSERT INTO " . $this->table . " (id_user, id_produk) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $product_id]);
    }

    public function removeItem($user_id, $product_id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $product_id]);
    }

    public function getItems($user_id) {
        $sql = "SELECT w.*, p.judul, p.harga, p.tipe_barang, p.foto1, p.status as status_produk, p.kondisi,
                       u.nama as nama_penjual, u.lokasi_kos, u.nomor_wa, u.angkatan, kat.nama_kategori
                FROM " . $this->table . " w
                LEFT JOIN produk p ON w.id_produk = p.id_produk
                LEFT JOIN users u ON p.id_user = u.id_user
                LEFT JOIN kategori kat ON p.id_kategori = kat.id_kategori
                WHERE w.id_user = ? AND p.status = 'tersedia'
                ORDER BY w.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemCount($user_id) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " w
                LEFT JOIN produk p ON w.id_produk = p.id_produk
                WHERE w.id_user = ? AND p.status = 'tersedia'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function isItemInWishlist($user_id, $product_id) {
        $sql = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id, $product_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function clearWishlist($user_id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_user = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id]);
    }

    public function moveToCart($user_id, $product_id) {
        // First, add to cart
        require_once 'Cart.php';
        $cart = new Cart($this->conn);
        $added = $cart->addItem($user_id, $product_id);
        
        if ($added) {
            // Then remove from wishlist
            return $this->removeItem($user_id, $product_id);
        }
        
        return false;
    }

    public function getWishlistStats($user_id) {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                    COUNT(CASE WHEN p.tipe_barang = 'donasi' THEN 1 END) as free_items,
                    COUNT(CASE WHEN p.tipe_barang = 'jual' THEN 1 END) as paid_items,
                    AVG(CASE WHEN p.tipe_barang = 'jual' THEN p.harga END) as avg_price
                FROM " . $this->table . " w
                LEFT JOIN produk p ON w.id_produk = p.id_produk
                WHERE w.id_user = ? AND p.status = 'tersedia'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecentlyAdded($user_id, $limit = 5) {
        $sql = "SELECT w.*, p.judul, p.harga, p.tipe_barang, p.foto1, p.status as status_produk,
                       u.nama as nama_penjual, u.lokasi_kos, kat.nama_kategori
                FROM " . $this->table . " w
                LEFT JOIN produk p ON w.id_produk = p.id_produk
                LEFT JOIN users u ON p.id_user = u.id_user
                LEFT JOIN kategori kat ON p.id_kategori = kat.id_kategori
                WHERE w.id_user = ? AND p.status = 'tersedia'
                ORDER BY w.created_at DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserWishlistByCategory($user_id) {
        $sql = "SELECT kat.nama_kategori, COUNT(*) as count
                FROM " . $this->table . " w
                LEFT JOIN produk p ON w.id_produk = p.id_produk
                LEFT JOIN kategori kat ON p.id_kategori = kat.id_kategori
                WHERE w.id_user = ? AND p.status = 'tersedia'
                GROUP BY kat.id_kategori, kat.nama_kategori
                ORDER BY count DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
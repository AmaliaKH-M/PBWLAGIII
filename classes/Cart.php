<?php
class Cart {
    private $conn;
    private $table = 'keranjang';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addItem($user_id, $product_id, $jumlah = 1, $catatan = null) {
        // Check if item already exists in cart
        $stmt = $this->conn->prepare("SELECT id_keranjang FROM " . $this->table . " WHERE id_user = ? AND id_produk = ?");
        $stmt->execute([$user_id, $product_id]);
        
        if ($stmt->rowCount() > 0) {
            // Update existing item
            $sql = "UPDATE " . $this->table . " SET jumlah = jumlah + ?";
            $params = [$jumlah];
            
            if ($catatan) {
                $sql .= ", catatan = ?";
                $params[] = $catatan;
            }
            
            $sql .= " WHERE id_user = ? AND id_produk = ?";
            $params[] = $user_id;
            $params[] = $product_id;
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } else {
            // Insert new item
            $sql = "INSERT INTO " . $this->table . " (id_user, id_produk, jumlah, catatan) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$user_id, $product_id, $jumlah, $catatan]);
        }
    }

    public function getItems($user_id) {
        $sql = "SELECT k.*, p.judul, p.harga, p.tipe_barang, p.foto1, p.status as status_produk, 
                       u.nama as nama_penjual, u.lokasi_kos, u.nomor_wa, kat.nama_kategori
                FROM " . $this->table . " k
                LEFT JOIN produk p ON k.id_produk = p.id_produk
                LEFT JOIN users u ON p.id_user = u.id_user
                LEFT JOIN kategori kat ON p.id_kategori = kat.id_kategori
                WHERE k.id_user = ? AND p.status = 'tersedia'
                ORDER BY k.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemCount($user_id) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " k
                LEFT JOIN produk p ON k.id_produk = p.id_produk
                WHERE k.id_user = ? AND p.status = 'tersedia'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updateQuantity($user_id, $product_id, $jumlah) {
        if ($jumlah <= 0) {
            return $this->removeItem($user_id, $product_id);
        }
        
        $sql = "UPDATE " . $this->table . " SET jumlah = ? WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$jumlah, $user_id, $product_id]);
    }

    public function updateNote($user_id, $product_id, $catatan) {
        $sql = "UPDATE " . $this->table . " SET catatan = ? WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$catatan, $user_id, $product_id]);
    }

    public function removeItem($user_id, $product_id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id, $product_id]);
    }

    public function clearCart($user_id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_user = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id]);
    }

    public function getCartTotal($user_id) {
        $sql = "SELECT SUM(k.jumlah * CASE WHEN p.tipe_barang = 'donasi' THEN 0 ELSE p.harga END) as total
                FROM " . $this->table . " k
                LEFT JOIN produk p ON k.id_produk = p.id_produk
                WHERE k.id_user = ? AND p.status = 'tersedia'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?: 0;
    }

    public function isItemInCart($user_id, $product_id) {
        $sql = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE id_user = ? AND id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id, $product_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function getCartSummary($user_id) {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                    SUM(k.jumlah) as total_quantity,
                    SUM(CASE WHEN p.tipe_barang = 'donasi' THEN 0 ELSE k.jumlah * p.harga END) as total_price,
                    COUNT(CASE WHEN p.tipe_barang = 'donasi' THEN 1 END) as free_items,
                    COUNT(CASE WHEN p.tipe_barang = 'jual' THEN 1 END) as paid_items
                FROM " . $this->table . " k
                LEFT JOIN produk p ON k.id_produk = p.id_produk
                WHERE k.id_user = ? AND p.status = 'tersedia'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function moveToWishlist($user_id, $product_id) {
        // First, add to wishlist
        require_once 'Wishlist.php';
        $wishlist = new Wishlist($this->conn);
        $added = $wishlist->addItem($user_id, $product_id);
        
        if ($added) {
            // Then remove from cart
            return $this->removeItem($user_id, $product_id);
        }
        
        return false;
    }
}
?>
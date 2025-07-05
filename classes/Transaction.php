<?php
class Transaction {
    private $conn;
    private $table = 'transaksi';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createTransaction($data) {
        $sql = "INSERT INTO " . $this->table . " (id_produk, id_pembeli, id_penjual, harga_deal, tipe_transaksi, metode_pembayaran, catatan_pembeli) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute([
            $data['id_produk'],
            $data['id_pembeli'],
            $data['id_penjual'],
            $data['harga_deal'],
            $data['tipe_transaksi'],
            $data['metode_pembayaran'],
            $data['catatan_pembeli']
        ])) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    public function getTransactionById($id) {
        $sql = "SELECT t.*, p.judul as judul_produk, p.foto1, p.harga as harga_awal,
                       pembeli.nama as nama_pembeli, pembeli.nomor_wa as wa_pembeli, pembeli.lokasi_kos as lokasi_pembeli,
                       penjual.nama as nama_penjual, penjual.nomor_wa as wa_penjual, penjual.lokasi_kos as lokasi_penjual
                FROM " . $this->table . " t
                LEFT JOIN produk p ON t.id_produk = p.id_produk
                LEFT JOIN users pembeli ON t.id_pembeli = pembeli.id_user
                LEFT JOIN users penjual ON t.id_penjual = penjual.id_user
                WHERE t.id_transaksi = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserTransactions($user_id, $type = 'all') {
        $sql = "SELECT t.*, p.judul as judul_produk, p.foto1, p.harga as harga_awal,
                       pembeli.nama as nama_pembeli, pembeli.nomor_wa as wa_pembeli,
                       penjual.nama as nama_penjual, penjual.nomor_wa as wa_penjual
                FROM " . $this->table . " t
                LEFT JOIN produk p ON t.id_produk = p.id_produk
                LEFT JOIN users pembeli ON t.id_pembeli = pembeli.id_user
                LEFT JOIN users penjual ON t.id_penjual = penjual.id_user
                WHERE ";
        
        $params = [];
        
        if ($type == 'purchases') {
            $sql .= "t.id_pembeli = ?";
            $params[] = $user_id;
        } elseif ($type == 'sales') {
            $sql .= "t.id_penjual = ?";
            $params[] = $user_id;
        } else {
            $sql .= "(t.id_pembeli = ? OR t.id_penjual = ?)";
            $params[] = $user_id;
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY t.tanggal_transaksi DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTransactionStatus($id, $status) {
        $sql = "UPDATE " . $this->table . " SET status_transaksi = ?";
        $params = [$status];
        
        if ($status == 'selesai') {
            $sql .= ", tanggal_selesai = NOW()";
        }
        
        $sql .= " WHERE id_transaksi = ?";
        $params[] = $id;
        
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute($params);
        
        if ($success && $status == 'selesai') {
            // Update product status
            $transaction = $this->getTransactionById($id);
            if ($transaction) {
                $product_status = ($transaction['tipe_transaksi'] == 'donasi') ? 'terdonasi' : 'terjual';
                $this->updateProductStatus($transaction['id_produk'], $product_status);
            }
        }
        
        return $success;
    }

    private function updateProductStatus($product_id, $status) {
        $sql = "UPDATE produk SET status = ? WHERE id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $product_id]);
    }

    public function getTransactionStats($user_id = null) {
        $stats = [];
        
        if ($user_id) {
            // User-specific stats
            $sql = "SELECT 
                        COUNT(CASE WHEN id_pembeli = ? THEN 1 END) as total_purchases,
                        COUNT(CASE WHEN id_penjual = ? THEN 1 END) as total_sales,
                        COUNT(CASE WHEN id_pembeli = ? AND status_transaksi = 'selesai' THEN 1 END) as completed_purchases,
                        COUNT(CASE WHEN id_penjual = ? AND status_transaksi = 'selesai' THEN 1 END) as completed_sales,
                        SUM(CASE WHEN id_pembeli = ? AND status_transaksi = 'selesai' THEN harga_deal ELSE 0 END) as total_spent,
                        SUM(CASE WHEN id_penjual = ? AND status_transaksi = 'selesai' THEN harga_deal ELSE 0 END) as total_earned
                    FROM " . $this->table;
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Global stats
            $sql = "SELECT 
                        COUNT(*) as total_transactions,
                        COUNT(CASE WHEN status_transaksi = 'selesai' THEN 1 END) as completed_transactions,
                        COUNT(CASE WHEN tipe_transaksi = 'donasi' THEN 1 END) as donations,
                        COUNT(CASE WHEN tipe_transaksi = 'jual' THEN 1 END) as sales,
                        SUM(CASE WHEN status_transaksi = 'selesai' AND tipe_transaksi = 'jual' THEN harga_deal ELSE 0 END) as total_value
                    FROM " . $this->table;
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $stats;
    }

    public function getRecentTransactions($limit = 10) {
        $sql = "SELECT t.*, p.judul as judul_produk, p.foto1,
                       pembeli.nama as nama_pembeli,
                       penjual.nama as nama_penjual
                FROM " . $this->table . " t
                LEFT JOIN produk p ON t.id_produk = p.id_produk
                LEFT JOIN users pembeli ON t.id_pembeli = pembeli.id_user
                LEFT JOIN users penjual ON t.id_penjual = penjual.id_user
                ORDER BY t.tanggal_transaksi DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionsByStatus($status, $limit = null) {
        $sql = "SELECT t.*, p.judul as judul_produk, p.foto1,
                       pembeli.nama as nama_pembeli,
                       penjual.nama as nama_penjual
                FROM " . $this->table . " t
                LEFT JOIN produk p ON t.id_produk = p.id_produk
                LEFT JOIN users pembeli ON t.id_pembeli = pembeli.id_user
                LEFT JOIN users penjual ON t.id_penjual = penjual.id_user
                WHERE t.status_transaksi = ?
                ORDER BY t.tanggal_transaksi DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$status]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function canUserReview($user_id, $product_id) {
        $sql = "SELECT COUNT(*) as count FROM " . $this->table . " 
                WHERE (id_pembeli = ? OR id_penjual = ?) 
                AND id_produk = ? 
                AND status_transaksi = 'selesai'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id, $user_id, $product_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function getMonthlyTransactionStats($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                    MONTH(tanggal_transaksi) as month,
                    COUNT(*) as total_transactions,
                    COUNT(CASE WHEN status_transaksi = 'selesai' THEN 1 END) as completed_transactions,
                    SUM(CASE WHEN status_transaksi = 'selesai' AND tipe_transaksi = 'jual' THEN harga_deal ELSE 0 END) as total_value
                FROM " . $this->table . "
                WHERE YEAR(tanggal_transaksi) = ?
                GROUP BY MONTH(tanggal_transaksi)
                ORDER BY month";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$year]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function processCartCheckout($user_id, $payment_method = 'cod') {
        // Get cart items
        require_once 'Cart.php';
        $cart = new Cart($this->conn);
        $cart_items = $cart->getItems($user_id);
        
        if (empty($cart_items)) {
            return false;
        }
        
        $transaction_ids = [];
        
        foreach ($cart_items as $item) {
            $transaction_data = [
                'id_produk' => $item['id_produk'],
                'id_pembeli' => $user_id,
                'id_penjual' => $item['id_user'],
                'harga_deal' => $item['tipe_barang'] == 'donasi' ? 0 : $item['harga'],
                'tipe_transaksi' => $item['tipe_barang'],
                'metode_pembayaran' => $payment_method,
                'catatan_pembeli' => $item['catatan']
            ];
            
            $transaction_id = $this->createTransaction($transaction_data);
            if ($transaction_id) {
                $transaction_ids[] = $transaction_id;
            }
        }
        
        if (!empty($transaction_ids)) {
            // Clear cart after successful checkout
            $cart->clearCart($user_id);
        }
        
        return $transaction_ids;
    }
}
?>
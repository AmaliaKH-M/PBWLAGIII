THIS SHOULD BE A LINTER ERROR<?php
class Product {
    private $conn;
    private $table = 'produk';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (id_user, id_kategori, judul, deskripsi, harga, harga_asli, kondisi, tipe_barang, foto1, foto2, foto3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            $data['id_user'],
            $data['id_kategori'],
            $data['judul'],
            $data['deskripsi'],
            $data['harga'],
            $data['harga_asli'],
            $data['kondisi'],
            $data['tipe_barang'],
            $data['foto1'],
            $data['foto2'],
            $data['foto3']
        ]);
    }

    public function getAll($limit = null, $search = null, $kategori = null, $tipe = null, $user_id = null) {
        $sql = "SELECT p.*, k.nama_kategori, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual, u.nomor_wa 
                FROM " . $this->table . " p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                LEFT JOIN users u ON p.id_user = u.id_user 
                WHERE p.status = 'tersedia'";
        
        $params = [];
        
        if ($search) {
            $sql .= " AND (p.judul LIKE ? OR p.deskripsi LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        
        if ($kategori) {
            $sql .= " AND p.id_kategori = ?";
            $params[] = $kategori;
        }
        
        if ($tipe) {
            $sql .= " AND p.tipe_barang = ?";
            $params[] = $tipe;
        }
        
        if ($user_id) {
            $sql .= " AND p.id_user = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . $limit;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT p.*, k.nama_kategori, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual, u.nomor_wa, u.foto_profil as foto_penjual 
                FROM " . $this->table . " p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                LEFT JOIN users u ON p.id_user = u.id_user 
                WHERE p.id_produk = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Increment view count
            $this->incrementViews($id);
        }
        
        return $product;
    }

    public function getUserProducts($user_id, $status = null) {
        $sql = "SELECT p.*, k.nama_kategori 
                FROM " . $this->table . " p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                WHERE p.id_user = ?";
        
        $params = [$user_id];
        
        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        } else {
            $sql .= " AND p.status != 'dihapus'";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE " . $this->table . " SET judul = ?, deskripsi = ?, harga = ?, harga_asli = ?, kondisi = ?, tipe_barang = ?, id_kategori = ?";
        $params = [
            $data['judul'],
            $data['deskripsi'],
            $data['harga'],
            $data['harga_asli'],
            $data['kondisi'],
            $data['tipe_barang'],
            $data['id_kategori']
        ];
        
        if (isset($data['foto1'])) {
            $sql .= ", foto1 = ?";
            $params[] = $data['foto1'];
        }
        
        if (isset($data['foto2'])) {
            $sql .= ", foto2 = ?";
            $params[] = $data['foto2'];
        }
        
        if (isset($data['foto3'])) {
            $sql .= ", foto3 = ?";
            $params[] = $data['foto3'];
        }
        
        $sql .= " WHERE id_produk = ?";
        $params[] = $id;
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE " . $this->table . " SET status = ? WHERE id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $sql = "UPDATE " . $this->table . " SET status = 'dihapus' WHERE id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function incrementViews($id) {
        $sql = "UPDATE " . $this->table . " SET views = views + 1 WHERE id_produk = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getCategories() {
        $sql = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $sql = "SELECT * FROM kategori WHERE id_kategori = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($kategori_id, $produk_id, $limit = 4) {
        $sql = "SELECT p.*, k.nama_kategori, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual 
                FROM " . $this->table . " p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                LEFT JOIN users u ON p.id_user = u.id_user 
                WHERE p.id_kategori = ? AND p.id_produk != ? AND p.status = 'tersedia'
                ORDER BY p.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$kategori_id, $produk_id, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductStats() {
        $stats = [];
        
        // Total produk
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE status != 'dihapus'");
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk tersedia
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'tersedia'");
        $stmt->execute();
        $stats['tersedia'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk terjual
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'terjual'");
        $stmt->execute();
        $stats['terjual'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk terdonasi
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'terdonasi'");
        $stmt->execute();
        $stats['terdonasi'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk gratis
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE tipe_barang = 'donasi' AND status = 'tersedia'");
        $stmt->execute();
        $stats['gratis'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $stats;
    }

    public function getPopularProducts($limit = 10) {
        $sql = "SELECT p.*, k.nama_kategori, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual, u.angkatan 
                FROM " . $this->table . " p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                LEFT JOIN users u ON p.id_user = u.id_user 
                WHERE p.status = 'tersedia'
                ORDER BY p.views DESC, p.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($keyword, $filters = []) {
        $sql = "SELECT p.*, k.nama_kategori, u.nama as nama_penjual, u.lokasi_kos as lokasi_penjual, u.angkatan 
                FROM " . $this->table . " p 
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                LEFT JOIN users u ON p.id_user = u.id_user 
                WHERE p.status = 'tersedia'";
        
        $params = [];
        
        if ($keyword) {
            $sql .= " AND (p.judul LIKE ? OR p.deskripsi LIKE ? OR k.nama_kategori LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        if (isset($filters['kategori']) && $filters['kategori']) {
            $sql .= " AND p.id_kategori = ?";
            $params[] = $filters['kategori'];
        }
        
        if (isset($filters['tipe']) && $filters['tipe']) {
            $sql .= " AND p.tipe_barang = ?";
            $params[] = $filters['tipe'];
        }
        
        if (isset($filters['kondisi']) && $filters['kondisi']) {
            $sql .= " AND p.kondisi = ?";
            $params[] = $filters['kondisi'];
        }
        
        if (isset($filters['harga_min']) && $filters['harga_min']) {
            $sql .= " AND p.harga >= ?";
            $params[] = $filters['harga_min'];
        }
        
        if (isset($filters['harga_max']) && $filters['harga_max']) {
            $sql .= " AND p.harga <= ?";
            $params[] = $filters['harga_max'];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($nama, $email, $password, $nomor_wa, $lokasi_kos) {
        // Validate STIS email format
        if (!validateSTISEmail($email)) {
            return false;
        }

        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id_user FROM " . $this->table . " WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return false;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate angkatan from email
        $angkatan = generateAngkatan($email);

        // Insert user
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (nama, email, password, nomor_wa, lokasi_kos, angkatan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $nama);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $hashed_password);
        $stmt->bindParam(4, $nomor_wa);
        $stmt->bindParam(5, $lokasi_kos);
        $stmt->bindParam(6, $angkatan);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_user = ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $nama, $nomor_wa, $lokasi_kos, $foto_profil = null) {
        $sql = "UPDATE " . $this->table . " SET nama = ?, nomor_wa = ?, lokasi_kos = ?";
        $params = [$nama, $nomor_wa, $lokasi_kos];
        
        if ($foto_profil) {
            $sql .= ", foto_profil = ?";
            $params[] = $foto_profil;
        }
        
        $sql .= " WHERE id_user = ?";
        $params[] = $id;
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function changePassword($id, $old_password, $new_password) {
        // Get current password
        $stmt = $this->conn->prepare("SELECT password FROM " . $this->table . " WHERE id_user = ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($old_password, $user['password'])) {
            return false;
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET password = ? WHERE id_user = ?");
        $stmt->bindParam(1, $hashed_password);
        $stmt->bindParam(2, $id);
        
        return $stmt->execute();
    }

    public function getKosLocations() {
        // Static list of kos locations around STIS
        return [
            'Kos Putri Melati - Jl. Otto Iskandardinata',
            'Kos Putra Anggrek - Jl. Raya Cipayung',
            'Kos Wisma Indah - Jl. Raya Pondok Gede',
            'Kos Griya Asri - Jl. Raya Bogor',
            'Kos Harmoni - Jl. Raya Cibinong',
            'Kos Sari Indah - Jl. Raya Citeureup',
            'Kos Bunga Mawar - Jl. Raya Sentul',
            'Kos Permata Hijau - Jl. Raya Gunung Putri',
            'Kos Dahlia - Jl. Raya Tajur',
            'Kos Cemara - Jl. Raya Warung Nangka',
            'Lainnya (Sebutkan di form)'
        ];
    }

    public function getAllUsers($limit = null, $offset = null) {
        $sql = "SELECT id_user, nama, email, nomor_wa, lokasi_kos, angkatan, role, created_at FROM " . $this->table . " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . $limit;
            if ($offset) {
                $sql .= " OFFSET " . $offset;
            }
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStats($user_id) {
        $stats = [];
        
        // Total produk
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM produk WHERE id_user = ?");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $stats['total_produk'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk terjual
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM produk WHERE id_user = ? AND status = 'terjual'");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $stats['terjual'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk terdonasi
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM produk WHERE id_user = ? AND status = 'terdonasi'");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $stats['terdonasi'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Produk tersedia
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM produk WHERE id_user = ? AND status = 'tersedia'");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $stats['tersedia'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $stats;
    }
}
?>
-- Database: kosmarket_db
-- Struktur database untuk KosMarket Platform

CREATE DATABASE IF NOT EXISTS `kosmarket_db`;
USE `kosmarket_db`;

-- Tabel Users
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `nomor_wa` varchar(20) NOT NULL,
  `lokasi_kos` varchar(100) NOT NULL,
  `angkatan` varchar(4) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Kategori
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Produk
CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `harga_asli` decimal(10,2) DEFAULT NULL,
  `kondisi` enum('Baru','Seperti Baru','Baik','Cukup Baik','Butuh Perbaikan') DEFAULT 'Baik',
  `tipe_barang` enum('jual','donasi') DEFAULT 'jual',
  `foto1` varchar(255) DEFAULT NULL,
  `foto2` varchar(255) DEFAULT NULL,
  `foto3` varchar(255) DEFAULT NULL,
  `status` enum('tersedia','terjual','terdonasi','dihapus') DEFAULT 'tersedia',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_produk`),
  KEY `idx_user` (`id_user`),
  KEY `idx_kategori` (`id_kategori`),
  KEY `idx_status` (`status`),
  KEY `idx_tipe` (`tipe_barang`),
  CONSTRAINT `fk_produk_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Transaksi
CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `id_produk` int(11) NOT NULL,
  `id_pembeli` int(11) NOT NULL,
  `id_penjual` int(11) NOT NULL,
  `harga_deal` decimal(10,2) DEFAULT NULL,
  `tipe_transaksi` enum('jual','donasi') DEFAULT 'jual',
  `metode_pembayaran` enum('cash','transfer','cod') DEFAULT 'cod',
  `status_transaksi` enum('pending','diproses','selesai','dibatalkan') DEFAULT 'pending',
  `catatan_pembeli` text DEFAULT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_selesai` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `idx_produk` (`id_produk`),
  KEY `idx_pembeli` (`id_pembeli`),
  KEY `idx_penjual` (`id_penjual`),
  KEY `idx_status` (`status_transaksi`),
  CONSTRAINT `fk_transaksi_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `fk_transaksi_pembeli` FOREIGN KEY (`id_pembeli`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_transaksi_penjual` FOREIGN KEY (`id_penjual`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Keranjang
CREATE TABLE `keranjang` (
  `id_keranjang` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT 1,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_keranjang`),
  UNIQUE KEY `unique_user_produk` (`id_user`,`id_produk`),
  KEY `idx_user` (`id_user`),
  KEY `idx_produk` (`id_produk`),
  CONSTRAINT `fk_keranjang_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Wishlist
CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_wishlist`),
  UNIQUE KEY `unique_user_produk` (`id_user`,`id_produk`),
  KEY `idx_user` (`id_user`),
  KEY `idx_produk` (`id_produk`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data sample untuk kategori
INSERT INTO `kategori` (`nama_kategori`, `deskripsi`, `icon`, `color`) VALUES
('Pakaian', 'Baju, celana, jaket, dan aksesoris fashion', 'tshirt', '#FFCDB2'),
('Elektronik', 'Gadget, laptop, charger, dan perangkat elektronik', 'laptop', '#FFB4A2'),
('Buku & Alat Tulis', 'Buku kuliah, novel, alat tulis, dan perlengkapan belajar', 'book', '#E5989B'),
('Furnitur', 'Meja, kursi, lemari, dan perabotan kos', 'couch', '#B5828C'),
('Olahraga', 'Sepatu, baju olahraga, dan peralatan fitness', 'dumbbell', '#FFCDB2'),
('Kecantikan', 'Kosmetik, skincare, dan produk perawatan', 'heart', '#FFB4A2'),
('Makanan & Minuman', 'Snack, minuman, dan makanan kering', 'utensils', '#E5989B'),
('Lainnya', 'Barang-barang lain yang tidak masuk kategori di atas', 'box', '#B5828C');

-- Data sample untuk lokasi kos (akan digunakan untuk dropdown)
-- Sample user admin
INSERT INTO `users` (`nama`, `email`, `password`, `nomor_wa`, `lokasi_kos`, `angkatan`, `role`) VALUES
('Admin KosMarket', 'admin@kosmarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'Kampus STIS', '00', 'admin');
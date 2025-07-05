-- Database: kosmarket_db - SIMPLE VERSION (TANPA ANGKATAN)
-- Struktur database untuk KosMarket Platform

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS `kosmarket_db`;
CREATE DATABASE `kosmarket_db`;
USE `kosmarket_db`;

-- Tabel Users (TANPA ANGKATAN)
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `nomor_wa` varchar(20) NOT NULL,
  `lokasi_kos` varchar(100) NOT NULL,
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

-- Sample users (minimal 10 data - TANPA ANGKATAN)
INSERT INTO `users` (`nama`, `email`, `password`, `nomor_wa`, `lokasi_kos`, `role`) VALUES
('Admin KosMarket', 'admin@kosmarket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'Kampus STIS', 'admin'),
('Andi Pratama', '22161001@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456789', 'Kos Putri Melati - Jl. Otto Iskandardinata', 'user'),
('Budi Santoso', '22161002@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456790', 'Kos Putra Anggrek - Jl. Raya Cipayung', 'user'),
('Citra Dewi', '22161003@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456791', 'Kos Wisma Indah - Jl. Raya Pondok Gede', 'user'),
('Dedi Kurniawan', '21161004@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456792', 'Kos Griya Asri - Jl. Raya Bogor', 'user'),
('Eka Sari', '21161005@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456793', 'Kos Harmoni - Jl. Raya Cibinong', 'user'),
('Fajar Ahmad', '23161006@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456794', 'Kos Sari Indah - Jl. Raya Citeureup', 'user'),
('Gita Permata', '23161007@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456795', 'Kos Bunga Mawar - Jl. Raya Sentul', 'user'),
('Hendra Wijaya', '20161008@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456796', 'Kos Permata Hijau - Jl. Raya Gunung Putri', 'user'),
('Indira Salsabila', '20161009@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456797', 'Kos Dahlia - Jl. Raya Tajur', 'user'),
('Joko Susilo', '19161010@stis.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082123456798', 'Kos Cemara - Jl. Raya Warung Nangka', 'user');

-- Sample products (minimal 15 data for variety)
INSERT INTO `produk` (`id_user`, `id_kategori`, `judul`, `deskripsi`, `harga`, `harga_asli`, `kondisi`, `tipe_barang`, `foto1`, `status`) VALUES
(2, 1, 'Jaket Denim Unisex', 'Jaket denim warna biru, ukuran M, kondisi masih bagus sekali. Cocok untuk kuliah atau hangout.', 85000, 150000, 'Seperti Baru', 'jual', 'jaket_denim.jpg', 'tersedia'),
(3, 2, 'Laptop Lenovo ThinkPad', 'Laptop bekas kuliah, spek i5, RAM 8GB, SSD 256GB. Masih mulus dan lancar untuk coding.', 3500000, 6000000, 'Baik', 'jual', 'laptop_lenovo.jpg', 'tersedia'),
(4, 3, 'Buku Statistika Dasar', 'Buku kuliah statistika lengkap dengan catatan. Cocok untuk mahasiswa semester awal.', 45000, 85000, 'Baik', 'jual', 'buku_statistika.jpg', 'tersedia'),
(5, 4, 'Meja Belajar Lipat', 'Meja belajar praktis bisa dilipat, hemat tempat. Cocok untuk kamar kos yang sempit.', 0, 200000, 'Cukup Baik', 'donasi', 'meja_lipat.jpg', 'tersedia'),
(6, 5, 'Sepatu Running Nike', 'Sepatu lari Nike Air Max, ukuran 42, masih terawat. Jarang dipakai karena jarang olahraga.', 350000, 800000, 'Seperti Baru', 'jual', 'sepatu_nike.jpg', 'tersedia'),
(7, 6, 'Set Skincare Korea', 'Paket skincare lengkap dari Korea, baru beli tapi tidak cocok di kulit. Masih segel.', 120000, 200000, 'Baru', 'jual', 'skincare_korea.jpg', 'tersedia'),
(8, 7, 'Snack Import Jepang', 'Aneka snack import dari Jepang, expired masih lama. Beli kebanyakan jadi dijual murah.', 25000, 50000, 'Baru', 'jual', 'snack_jepang.jpg', 'tersedia'),
(9, 1, 'Kemeja Formal Putih', 'Kemeja putih untuk presentasi, ukuran L, kondisi sangat baik. Baru pakai 2-3 kali.', 55000, 120000, 'Seperti Baru', 'jual', 'kemeja_putih.jpg', 'tersedia'),
(10, 2, 'Power Bank 20000mAh', 'Power bank kapasitas besar, masih awet dan bisa fast charging. Lengkap dengan kabel.', 75000, 150000, 'Baik', 'jual', 'powerbank.jpg', 'tersedia'),
(3, 3, 'Novel Bestseller', 'Koleksi novel bestseller Indonesia dan luar negeri. Kondisi masih bagus semua.', 0, 300000, 'Baik', 'donasi', 'novel_koleksi.jpg', 'tersedia'),
(4, 4, 'Lemari Plastik 3 Susun', 'Lemari plastik untuk menyimpan pakaian, praktis dan ringan. Cocok untuk kos.', 0, 180000, 'Cukup Baik', 'donasi', 'lemari_plastik.jpg', 'tersedia'),
(5, 5, 'Raket Badminton Yonex', 'Raket badminton Yonex original, masih kencang senarnya. Jarang dipakai.', 180000, 350000, 'Baik', 'jual', 'raket_yonex.jpg', 'tersedia'),
(6, 6, 'Makeup Set Lengkap', 'Set makeup lengkap untuk pemula, masih banyak dan tidak expired. Cocok untuk kuliah.', 95000, 200000, 'Seperti Baru', 'jual', 'makeup_set.jpg', 'tersedia'),
(7, 7, 'Kopi Arabika Premium', 'Kopi arabika premium dari Aceh, masih dalam kemasan asli. Rasa mantap untuk ngopi malam.', 40000, 75000, 'Baru', 'jual', 'kopi_arabika.jpg', 'tersedia'),
(8, 8, 'Alat Tulis Lengkap', 'Paket alat tulis lengkap: pulpen, pensil, penghapus, penggaris. Cocok untuk mahasiswa baru.', 0, 50000, 'Baru', 'donasi', 'alat_tulis.jpg', 'tersedia');
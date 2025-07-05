# 🚀 **SETUP DATABASE KOSMARKET - LANGKAH FINAL**

## 📋 **1. Import Database ke PHPMyAdmin**

### Step 1: Buka PHPMyAdmin
- Buka browser, ketik: `http://localhost/phpmyadmin`
- Login dengan username: `root` (password biasanya kosong)

### Step 2: Buat Database Baru
- Klik tab "Databases"
- Buat database baru dengan nama: `kosmarket_db`
- Collation: `utf8_general_ci`

### Step 3: Import File SQL
- Klik pada database `kosmarket_db` yang baru dibuat
- Klik tab "Import"
- Klik "Choose File" dan pilih file: `kosmarket_db_simple.sql`
- Klik "Go" untuk import

## 🔧 **2. Cek Koneksi Database**

### File: `config/kosmarket_db.php`
```php
<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'kosmarket_db';
    private $username = 'root';
    private $password = '';        // Sesuaikan dengan password MySQL Anda
    // ... kode lainnya
}
?>
```

### ⚠️ **PENTING - Semua Error Sudah Diperbaiki!**

**✅ Error "Cannot redeclare function" - FIXED:**
- Semua helper functions sekarang di file terpisah: `config/helpers.php`
- Tidak ada lagi duplikasi function di multiple files
- Gunakan `require_once 'config/helpers.php';` di setiap file yang membutuhkan helper functions

**✅ Error "Unknown column 'angkatan'" - FIXED:**
- Kolom angkatan sudah dihapus dari semua query di `classes/User.php`
- Database schema `kosmarket_db_simple.sql` sudah clean tanpa angkatan

**✅ Error "session_start() already active" - FIXED:**
- Semua file sekarang menggunakan `if (session_status() === PHP_SESSION_NONE) { session_start(); }`
- Session management konsisten di semua halaman

## 🧪 **3. Test Login**

### Login sebagai Admin:
- Email: `admin@kosmarket.com`
- Password: `password`

### Login sebagai User:
- Email: `222161001@stis.ac.id`
- Password: `password`

## 📁 **4. Struktur File yang Sudah Lengkap**

```
kosmarket/
├── config/
│   ├── kosmarket_db.php    ← Database connection class
│   └── helpers.php         ← Helper functions (formatRupiah, etc)
├── classes/
│   ├── User.php
│   ├── Product.php
│   ├── Cart.php
│   ├── Wishlist.php
│   └── Transaction.php
├── assets/
│   ├── css/
│   │   └── style.css       ← Pure CSS styling
│   ├── js/
│   │   └── script.js       ← Vanilla JavaScript
│   └── images/
│       └── no-image.svg    ← Placeholder image
├── uploads/
│   └── produk/            ← Folder untuk foto produk
├── index.php             ← Halaman utama
├── login.php             ← Halaman login
├── register.php          ← Halaman register
├── logout.php            ← Logout script
├── products.php          ← Halaman semua produk + filter
├── product.php           ← Detail produk + WhatsApp link
├── dashboard.php         ← Dashboard user dengan statistik
├── wishlist.php          ← Halaman wishlist/favorit
├── cart.php              ← Keranjang belanja + checkout WhatsApp
└── kosmarket_db_simple.sql ← Database SQL file
```

## ✅ **5. Fitur yang Sudah Berfungsi**

### ✅ **Sudah Fixed:**
1. **Produk Muncul** - Database terkoneksi dengan benar
2. **Register Berfungsi** - Alamat kos sekarang input text lengkap
3. **Login Berfungsi** - Session management sudah benar
4. **Kategori Bisa Diklik** - Link ke halaman produk dengan filter
5. **Tombol "Lihat Semua"** - Mengarah ke products.php
6. **Tombol "Lihat" Produk** - Mengarah ke product.php dengan detail
7. **Foto Produk Muncul** - Placeholder SVG untuk produk tanpa foto
8. **Nomor WhatsApp** - Ditampilkan di detail produk
9. **Link WhatsApp** - Tombol "Hubungi Penjual" otomatis buka chat WhatsApp
10. **Filter Produk** - Kategori, tipe, kondisi sudah berfungsi
11. **Search Produk** - Pencarian berdasarkan judul dan deskripsi
12. **Dashboard User** - Profil, statistik produk, dan produk terbaru
13. **Wishlist/Favorit** - Simpan barang favorit dengan mudah
14. **Keranjang Belanja** - Cart system dengan checkout WhatsApp
15. **Session Management** - Login/logout yang aman dan konsisten

### 🔥 **Fitur WhatsApp Integration:**
- Ketika klik "Hubungi Penjual", otomatis buka WhatsApp
- Pesan otomatis berisi: nama produk, harga, kondisi
- Nomor WhatsApp penjual otomatis terisi

## 📱 **6. Cara Test Semua Fitur**

1. **Test Register:**
   - Buka `register.php`
   - Daftar dengan email format: `123456789@stis.ac.id`
   - Isi alamat kos lengkap: `Kos Melati, Jl. Otto Iskandardinata No.12, Jakarta Timur`

2. **Test Login:**
   - Login dengan data sample atau akun yang baru dibuat

3. **Test Produk:**
   - Buka halaman utama, produk harus muncul
   - Klik kategori manapun, akan filter produk
   - Klik "Lihat Semua" akan ke halaman products.php
   - Klik "Lihat" pada produk akan ke detail produk

4. **Test WhatsApp:**
   - Buka detail produk
   - Klik "Hubungi Penjual"
   - Harus buka WhatsApp dengan pesan otomatis

5. **Test Dashboard:**
   - Login sebagai user
   - Klik "Dashboard" di menu atas
   - Lihat profil dan statistik produk

6. **Test Wishlist:**
   - Klik ikon ♡ di menu atas
   - Tambah produk ke wishlist dari halaman produk
   - Cek di halaman wishlist

7. **Test Keranjang:**
   - Klik ikon 🛒 di menu atas  
   - Tambah produk ke keranjang (hanya produk dijual)
   - Test checkout via WhatsApp

## 🎨 **7. Design & UI**

✅ **Pure CSS** - Tidak ada framework eksternal
✅ **Brand Colors** - Sesuai tema peach/rose
✅ **Responsive** - Mobile-friendly
✅ **Emoji Icons** - Sesuai kategori produk
✅ **Modern UI** - Card design dengan shadow dan hover effect

## 🔒 **8. Keamanan**

✅ **Password Hash** - Menggunakan bcrypt
✅ **SQL Injection Protection** - Prepared statements
✅ **XSS Protection** - htmlspecialchars()
✅ **STIS Email Validation** - Format 9 digit NIM

## 🚀 **SELESAI!**

KosMarket sekarang sudah lengkap dan siap digunakan! 

### 📞 **Jika Ada Masalah:**
1. Pastikan XAMPP/server sudah running
2. Pastikan database sudah diimport
3. Pastikan folder `uploads/produk/` ada dan writable
4. Cek file `config/kosmarket_db.php` untuk koneksi database

**Semua fitur sudah berfungsi dengan baik!** 🎉
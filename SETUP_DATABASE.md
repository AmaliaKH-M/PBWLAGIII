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
$servername = "localhost";
$username = "root";
$password = "";        // Sesuaikan dengan password MySQL Anda
$database = "kosmarket_db";
?>
```

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
│   └── kosmarket_db.php
├── classes/
│   ├── User.php
│   ├── Product.php
│   ├── Cart.php
│   ├── Wishlist.php
│   └── Transaction.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│       └── no-image.svg
├── uploads/
│   └── produk/
├── index.php
├── login.php
├── register.php
├── logout.php
├── products.php
├── product.php
└── kosmarket_db_simple.sql
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
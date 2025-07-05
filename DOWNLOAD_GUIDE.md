# 📥 DOWNLOAD GUIDE - KosMarket Project

## 🎯 QUICK START - Copy Files Dalam Urutan Ini:

### 1️⃣ **BUAT STRUKTUR FOLDER**
```
kosmarket/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── ajax/
├── classes/
├── config/
└── uploads/
    └── produk/
```

### 2️⃣ **FILE WAJIB - COPY DALAM URUTAN INI:**

#### **A. Database (PRIORITAS UTAMA)**
1. `kosmarket_db.sql` ⭐ MOST IMPORTANT
2. `config/kosmarket_db.php` ⭐ MOST IMPORTANT

#### **B. PHP Pages (CORE)**
3. `index.php` ⭐ MOST IMPORTANT
4. `login.php` 
5. `register.php`
6. `logout.php`

#### **C. PHP Classes (BACKEND)**
7. `classes/User.php`
8. `classes/Product.php`
9. `classes/Cart.php`
10. `classes/Wishlist.php`
11. `classes/Transaction.php`

#### **D. Frontend Assets**
12. `assets/css/style.css` ⭐ MOST IMPORTANT
13. `assets/js/script.js`

#### **E. AJAX (LIVE SEARCH)**
14. `ajax/search_suggestions.php`

#### **F. Documentation**
15. `README.md`
16. `IMPLEMENTATION_SUMMARY.md`

## 🔧 **SETUP SETELAH COPY:**

### **1. Import Database**
```bash
mysql -u root -p
CREATE DATABASE kosmarket_db;
USE kosmarket_db;
SOURCE kosmarket_db.sql;
```

### **2. Update Config Database**
Edit `config/kosmarket_db.php`:
```php
private $host = 'localhost';        // Sesuaikan
private $db_name = 'kosmarket_db';   // Jangan ubah
private $username = 'root';          // Sesuaikan
private $password = '';              // Sesuaikan
```

### **3. Set Permissions**
```bash
chmod -R 755 assets/
chmod -R 755 uploads/
```

### **4. Test Login**
- **Email:** `22161001@stis.ac.id`
- **Password:** `password`

## ⚡ **MINIMAL FILES UNTUK TESTING:**

Jika mau test dulu, copy minimal files ini:
1. `kosmarket_db.sql` 
2. `config/kosmarket_db.php`
3. `index.php`
4. `login.php` 
5. `register.php`
6. `assets/css/style.css`
7. `classes/User.php`
8. `classes/Product.php`

## 🎯 **PRIORITAS COPY:**

### **TINGKAT 1 (WAJIB BANGET):**
- `kosmarket_db.sql`
- `config/kosmarket_db.php` 
- `index.php`
- `assets/css/style.css`

### **TINGKAT 2 (CORE FEATURES):**
- `login.php`, `register.php`
- `classes/User.php`, `classes/Product.php`
- `assets/js/script.js`

### **TINGKAT 3 (ADVANCED):**
- `classes/Cart.php`, `classes/Wishlist.php`
- `ajax/search_suggestions.php`
- Documentation files

## 🚨 **CATATAN PENTING:**

1. **SEMUA FILE SUDAH SIAP PAKAI** - tinggal copy paste
2. **JANGAN LUPA** buat folder `uploads/produk/` untuk upload gambar
3. **DATABASE SUDAH ADA SAMPLE DATA** 10+ users dan 15+ products
4. **LIVE SEARCH SUDAH AKTIF** dengan AJAX
5. **NO FRAMEWORK** - 100% pure HTML/CSS/JS/PHP

## 📱 **TESTING CHECKLIST:**

- [ ] Database berhasil diimport
- [ ] Homepage loading dengan categories
- [ ] Register dengan email STIS format
- [ ] Login berhasil
- [ ] Live search berfungsi (ketik 2+ huruf)
- [ ] Responsive design di mobile

## 🎉 **SELESAI!**

Setelah copy semua file, project KosMarket siap digunakan!
Total ada **sekitar 15-20 file** yang perlu dicopy.
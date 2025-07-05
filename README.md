# KosMarket - Platform Jual Beli & Donasi STIS

KosMarket adalah platform web komprehensif untuk komunitas STIS dan penghuni kos, dirancang untuk memfasilitasi jual-beli barang preloved dan sistem donasi barang layak pakai, menciptakan ekosistem saling bantu di lingkungan kampus.

## Fitur Utama

### 🎨 Desain Visual
- Tampilan dominan putih dengan aksen warna `#FFCDB2`, `#FFB4A2`, `#E5989B`, dan `#B5828C`
- Image Slider visual dengan Status Banner diagonal 3D (DIJUAL/GRATIS)
- Mode terang/gelap dengan kontras yang nyaman untuk penderita buta warna
- Logo "K❤️sMarket" dengan font Script untuk logo dan Poppins untuk teks

### 🔐 Sistem Autentikasi STIS
- Registrasi eksklusif untuk email `@stis.ac.id` dengan format `[0-9]{8}@stis.ac.id`
- Login sederhana dengan email dan password minimal 6 karakter
- Auto-login setelah registrasi berhasil
- Validasi angkatan otomatis dari NIM

### 👤 Profil Pengguna
- Nama lengkap dan foto profil dengan fitur auto-crop
- Nomor WhatsApp untuk komunikasi
- Pilihan lokasi kos dari dropdown area sekitar STIS
- Badge angkatan berdasarkan NIM

### 📦 Upload Barang
- Upload hingga 3 foto dengan drag & drop
- Preview slider sebelum publikasi
- Status banner otomatis (DIJUAL/GRATIS)
- Kompresi gambar otomatis
- Validasi formulir dengan feedback visual

### 🏠 Homepage & Detail Produk
- Feed responsif dengan grid layout
- Image slider dengan indikator
- Status banner di pojok produk
- Galeri gambar dengan fullscreen view
- Fitur zoom dan navigasi thumbnail

### 📊 Dashboard Pengguna
- Tab "Barang Saya" dengan status dan quick actions
- Tab "Favorit" untuk wishlist
- Counter total barang, terjual, dan terdonasi
- Statistik personal

### 👨‍💼 Panel Admin
- Manajemen pengguna dan produk
- Penghapusan konten tidak pantas
- Statistik platform
- Backup database

## Instalasi

### Prerequisites
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi  
- Apache/Nginx web server
- Composer (opsional)

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/kosmarket.git
   cd kosmarket
   ```

2. **Setup Database**
   ```bash
   mysql -u root -p
   ```
   
   Kemudian import file SQL:
   ```sql
   source kosmarket_db.sql;
   ```

3. **Konfigurasi Database**
   Edit file `config/kosmarket_db.php` sesuai dengan pengaturan database Anda:
   ```php
   private $host = 'localhost';
   private $db_name = 'kosmarket_db';
   private $username = 'root';
   private $password = '';
   ```

4. **Set Permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 assets/
   ```

5. **Akses Website**
   Buka browser dan akses: `http://localhost/kosmarket`

## Struktur Database

Database `kosmarket_db` berisi 6 tabel utama:

### 1. `users` - Data Pengguna
- `id_user` (Primary Key)
- `nama` - Nama lengkap
- `email` - Email STIS (@stis.ac.id)
- `password` - Password terenkripsi
- `foto_profil` - Path foto profil
- `nomor_wa` - Nomor WhatsApp
- `lokasi_kos` - Lokasi kos
- `angkatan` - Angkatan (2 digit pertama NIM)
- `role` - Role pengguna (user/admin)

### 2. `kategori` - Kategori Produk
- `id_kategori` (Primary Key)
- `nama_kategori` - Nama kategori
- `deskripsi` - Deskripsi kategori
- `icon` - Icon FontAwesome
- `color` - Warna kategori

### 3. `produk` - Data Produk
- `id_produk` (Primary Key)
- `id_user` - ID pemilik (Foreign Key)
- `id_kategori` - ID kategori (Foreign Key)
- `judul` - Judul produk
- `deskripsi` - Deskripsi produk
- `harga` - Harga jual
- `harga_asli` - Harga asli (coret)
- `kondisi` - Kondisi barang
- `tipe_barang` - jual/donasi
- `foto1`, `foto2`, `foto3` - Path foto produk
- `status` - tersedia/terjual/terdonasi/dihapus
- `views` - Jumlah views

### 4. `transaksi` - Data Transaksi
- `id_transaksi` (Primary Key)
- `id_produk` - ID produk (Foreign Key)
- `id_pembeli` - ID pembeli (Foreign Key)
- `id_penjual` - ID penjual (Foreign Key)
- `harga_deal` - Harga kesepakatan
- `tipe_transaksi` - jual/donasi
- `metode_pembayaran` - cash/transfer/cod
- `status_transaksi` - pending/diproses/selesai/dibatalkan
- `catatan_pembeli` - Catatan dari pembeli

### 5. `keranjang` - Keranjang Belanja
- `id_keranjang` (Primary Key)
- `id_user` - ID pengguna (Foreign Key)
- `id_produk` - ID produk (Foreign Key)
- `jumlah` - Jumlah item
- `catatan` - Catatan pembelian

### 6. `wishlist` - Daftar Favorit
- `id_wishlist` (Primary Key)
- `id_user` - ID pengguna (Foreign Key)
- `id_produk` - ID produk (Foreign Key)

## Fitur Teknis

### Email Validation
- Format email: `[0-9]{8}@stis.ac.id`
- Validasi dilakukan di frontend dan backend
- Angkatan otomatis dari 2 digit pertama NIM

### File Upload
- Maximum 3 foto per produk
- Auto-resize dan kompresi
- Format yang didukung: JPG, PNG, WebP
- Drag & drop interface

### Responsive Design
- Mobile-first approach
- Breakpoint: 768px untuk tablet/mobile
- Touch-friendly interface
- Optimized untuk semua ukuran layar

### Security
- Password hashing dengan bcrypt
- SQL injection protection dengan PDO
- XSS protection
- CSRF protection
- Session management

## Struktur Folder

```
kosmarket/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
├── classes/
│   ├── Cart.php
│   ├── Product.php
│   ├── Transaction.php
│   ├── User.php
│   └── Wishlist.php
├── config/
│   └── kosmarket_db.php
├── uploads/
│   └── produk/
├── index.php
├── login.php
├── register.php
├── products.php
├── sell.php
├── kosmarket_db.sql
└── README.md
```

## Kustomisasi

### Warna Brand
Warna utama dapat diubah di file `assets/css/style.css`:
```css
:root {
    --primary-peach: #FFCDB2;
    --secondary-peach: #FFB4A2;
    --tertiary-rose: #E5989B;
    --quaternary-mauve: #B5828C;
}
```

### Lokasi Kos
Daftar lokasi kos dapat ditambah di method `getKosLocations()` di file `classes/User.php`.

### Kategori Produk
Kategori dapat dikelola melalui database atau menambahkan seeder di file SQL.

## Kontribusi

1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## Kontak

- Email: admin@kosmarket.com
- WhatsApp: +62 812-3456-7890
- GitHub: [@username](https://github.com/username)

## Acknowledgments

- [Font Awesome](https://fontawesome.com) untuk icon
- [Google Fonts](https://fonts.google.com) untuk font Poppins dan Dancing Script
- Komunitas STIS untuk inspirasi dan feedback
# 🛒 Sistem Kasir Modern (POS - Point of Sale)

Aplikasi **Sistem Kasir (Point of Sale)** berbasis web yang modern, cepat, dan responsif dengan sistem **Multi-Role (Admin & Kasir)**. Dibangun menggunakan arsitektur terpisah (*decoupled architecture*): **Laravel** sebagai backend RESTful API yang aman (Sanctum) dan **Nuxt 4 / Vue 3 + Tailwind CSS** sebagai antarmuka pengguna yang interaktif.

---

## 🌟 Fitur Berdasarkan Peran (Role-Based Access)

Aplikasi memisahkan antarmuka dan hak akses secara ketat antara **Administrator** dan **Staff Kasir**:

| Fitur / Halaman | Staff Kasir | Administrator |
|---|:---:|:---:|
| 📈 **Dashboard Ringkasan & Grafik** | ❌ Disembunyikan | ✅ Akses Penuh (Halaman Utama) |
| 🛒 **Halaman Kasir (POS)** | ✅ Akses Penuh (Halaman Utama) | ❌ Disembunyikan |
| 📦 **Kelola Barang & Inventori** | ✅ Akses Penuh | ✅ Akses Penuh |
| 📜 **Riwayat Transaksi & Struk** | ✅ Akses Penuh | ✅ Akses Penuh |
| 📊 **Activity User Log** | ❌ Disembunyikan | ✅ Akses Penuh |
| 👥 **Kelola Pengguna (CRUD Users)** | ❌ Disembunyikan | ✅ Akses Penuh |
| ⚙️ **Pengaturan Toko & Logo Struk** | ❌ Disembunyikan | ✅ Akses Penuh |
| 🚫 **Batalkan (Void Transaksi)** | ❌ Disembunyikan | ✅ Diizinkan |

---

## 🚀 Fitur Lengkap

### 1. 📈 Dashboard Eksekutif Admin (Khusus Administrator)
- **Omzet Real-time:** Pemantauan omzet hari ini dan bulan ini beserta persentase perbandingan periode sebelumnya (kemarin & bulan lalu).
- **Volume & Total Transaksi:** Ringkasan jumlah transaksi hari ini, bulan ini, serta total transaksi selesai keseluruhan.
- **Peringatan Stok Kritis:** Deteksi dini barang dengan stok menipis (sisa ≤ 5 unit) atau habis (0) untuk kebutuhan restock cepat.
- **5 Produk Terlaris (Best Seller):** Peringkat produk dengan penjualan terbanyak lengkap dengan nominal omzet dan persentase kontribusi volume penjualan.
- **Grafik Interaktif:** Visualisasi tren pendapatan harian dan jumlah transaksi (7 Hari / 30 Hari Terakhir) lengkap dengan tooltip interaktif saat di-hover.
- **Distribusi Pembayaran:** Ringkasan metode pembayaran yang digunakan pelanggan (Tunai, QRIS, Midtrans/Transfer).

### 2. ⚙️ Pengaturan Profil Toko & Struk (Khusus Administrator)
- **Identitas & Branding Toko Dinamis:** Mengatur Nama Toko, Alamat Lengkap, dan Nomor Telepon/WhatsApp yang otomatis tersinkronisasi ke bilah navigasi (Desktop Sidebar & Mobile Header), header struk kasir, hingga layar login.
- **Logo Usaha Kustom:** Mendukung unggah logo format gambar (PNG, JPG, SVG, WebP) yang disimpan secara aman dalam format Base64 dan dirender otomatis pada struk dan navigasi.
- **Pesan Footer Struk:** Mengatur pesan kustom di bagian bawah struk belanja pelanggan (misal: *“Terima kasih atas kunjungan Anda!”* atau kebijakan retur barang).
- **Live Thermal Receipt Preview:** Simulator kertas struk belanja kasir *real-time* yang langsung menampilkan hasil cetak struk sesuai identitas dan logo yang sedang dikonfigurasikan.

### 3. 🛒 Kasir & Transaksi POS (Khusus Staff Kasir)
- **Katalog Produk Interaktif:** Pencarian instan (shortcut `F2`), filter kategori (*category chips*), dan pengurutan (nama, harga, stok).
- **Auto-Hide Out of Stock:** Produk dengan stok habis otomatis disembunyikan dari katalog transaksi kasir.
- **Kalkulasi Otomatis:** Perhitungan subtotal, diskon, pajak, dan nominal uang kembalian instan.
- **Multi-Metode Pembayaran:**
  - **Tunai (Cash)** dengan kalkulator pecahan cepat uang pas & kembalian.
  - **Digital Payment Gateway (Midtrans Snap):** QRIS (GoPay, ShopeePay, OVO), Virtual Account Bank (BCA, BNI, BRI, Mandiri), dan Kartu Kredit.
- **Cetak Struk Thermal:** Integrasi pencetakan ke printer thermal ESC/POS 58mm/80mm dan cetak browser.
- **Dukungan Batal Transaksi:** Membatalkan transaksi kasir yang belum selesai tanpa merusak sinkronisasi data Midtrans.

### 4. 📦 Manajemen Inventori & Barang (Kasir & Administrator)
- **CRUD Produk:** Tambah, edit, dan hapus barang lengkap dengan barcode, gambar, kategori, harga modal (HPP), harga jual, dan stok.
- **Penyesuaian Stok Cepat:** Tombol tambah stok masuk tanpa perlu edit seluruh data barang.
- **Bulk Add & Import Massal:** Form input dinamis banyak barang sekaligus serta import massal via file CSV/Spreadsheet.

### 5. 📊 Activity User Log (Khusus Administrator)
- **Pemantauan Linimasa Aktivitas:** Mencatat setiap aktivitas penting sistem secara real-time:
  - 🔐 Login & Logout pengguna (beserta alamat IP client).
  - 🛒 Pembuatan transaksi POS baru beserta nominal dan metode bayar.
  - 🚫 Pembatalan (void) transaksi beserta restock produk otomatis.
  - 📦 Penambahan, perubahan stok, dan penghapusan barang.
  - 👥 Penambahan akun, perubahan role, dan reset password.
  - ⚙️ Perubahan profil toko, logo, dan pengaturan struk.
- **Filter & Statistik:** Ringkasan total aktivitas, aktivitas hari ini, filter berdasarkan role, kategori aksi, dan tanggal.

### 6. 👥 Manajemen Pengguna (Khusus Administrator)
- **Kelola Akun:** Melihat daftar semua pengguna sistem, menambah akun baru, mengedit profil, dan mengganti role (*Admin / Kasir*).
- **Reset Password:** Mengubah password akun staf kasir secara langsung.
- **Proteksi Akun:** Mencegah administrator menghapus atau menurunkan (*demote*) role akunnya sendiri yang sedang aktif.

### 7. 📜 Riwayat Transaksi & Pelaporan
- Pencatatan seluruh transaksi penjualan lengkap dengan status (*Pending, Lunas, Dibatalkan*).
- Filter transaksi berdasarkan rentang tanggal, status, dan metode pembayaran.
- Cetak ulang struk transaksi dan pembatalan (*void*) dengan pengembalian stok otomatis.
- Fitur ekspor laporan transaksi ke format spreadsheet CSV.

### 8. 🧭 Desain Navigasi Responsif & Modern
- **Desktop Sidebar:** Navigasi vertikal tetap (*sticky sidebar*) elegan bernuansa *dark slate* lengkap dengan logo toko, identitas akun, role badge, dan pengelompokan menu rapi.
- **Mobile Drawer & Topbar:** Bilah navigasi ramping khusus perangkat layar kecil dengan drawer slide-over yang mulus.
- **Mobile Bottom Bar:** Navigasi bawah satu jempol (*bottom bar*) untuk akses cepat ke menu utama saat menggunakan tablet atau smartphone.

---

## 🛠️ Tech Stack

| Bagian | Teknologi |
|---|---|
| **Frontend** | [Nuxt 4](https://nuxt.com/) (Vue 3, Vite, Composition API), [Tailwind CSS](https://tailwindcss.com/) |
| **Backend** | [Laravel 12/13](https://laravel.com/) (PHP 8.2+ / 8.3+), [Laravel Sanctum](https://laravel.com/docs/sanctum) |
| **Database** | MySQL / SQLite |
| **Payment Gateway** | [Midtrans Snap API & Webhook Callback](https://midtrans.com/) |
| **Thermal Printer** | `mike42/escpos-php` |
| **Container / Server** | Docker, Laragon, Apache, Node.js |

---

## 📁 Struktur Direktori

```text
Sistem-kasir/
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── AuthController.php          # Login, Logout, Info User & Activity Log
│   │   │   │   ├── DashboardController.php     # Metrik Omzet, Best Seller, Stok Kritis & Grafik
│   │   │   │   ├── BarangController.php        # CRUD Barang & Bulk Import
│   │   │   │   ├── TransaksiController.php     # Transaksi POS, Void & Restock
│   │   │   │   ├── PaymentController.php       # Midtrans Snap & Webhook Callback
│   │   │   │   ├── UserController.php          # CRUD Pengguna & Reset Password
│   │   │   │   ├── ActivityController.php      # Pemantauan Log Aktivitas User
│   │   │   │   ├── SettingController.php       # Pengaturan Profil Toko, Kontak & Logo
│   │   │   │   └── PrintController.php         # Cetak Struk ESC/POS Printer Thermal
│   │   │   └── Middleware/
│   │   │       └── EnsureUserIsAdmin.php       # Middleware Proteksi Role Admin
│   │   └── Models/
│   │       ├── User.php                        # Model User (Role: Admin / Kasir)
│   │       ├── Barang.php                      # Model Produk & Stok
│   │       ├── Transaksi.php                   # Model Penjualan & Pembayaran
│   │       ├── ActivityLog.php                 # Model Pencatatan Log Aktivitas
│   │       └── TokoSetting.php                 # Model Pengaturan Profil Toko & Struk
│   ├── database/
│   │   ├── migrations/                         # Migrasi users, barangs, transaksis, activity_logs, toko_settings
│   │   └── seeders/
│   │       ├── UserSeeder.php                  # Akun default admin & kasir
│   │       ├── ActivityLogSeeder.php           # Contoh awal riwayat aktivitas
│   │       └── DatabaseSeeder.php
│   ├── routes/api.php                          # Endpoint API RESTful & Setting Toko
│   └── Dockerfile
├── frontend/
│   ├── app/
│   │   ├── composables/
│   │   │   ├── useAuth.js                      # State management user, role, token & helper isAdmin/isKasir
│   │   │   ├── useToast.js                     # Notifikasi toast global
│   │   │   └── useToko.js                      # Profil toko reaktif (Nama, Alamat, Telp, Logo, Footer Struk)
│   │   ├── layouts/
│   │   │   └── default.vue                     # Layout navigasi Sidebar Desktop & Mobile Drawer responsif
│   │   └── pages/
│   │       ├── index.vue                       # Login dengan routing otomatis sesuai role
│   │       ├── kasir.vue                       # Antarmuka Transaksi POS (Kasir Only)
│   │       ├── barang.vue                      # Manajemen Katalog & Stok (Kasir & Admin)
│   │       ├── riwayat.vue                     # Riwayat Penjualan & Cetak Struk (Kasir & Admin)
│   │       ├── dashboard.vue                   # Dashboard Metrik & Analitik Penjualan (Admin Only)
│   │       ├── activity.vue                    # Linimasa Activity User (Admin Only)
│   │       ├── users.vue                       # Kelola Pengguna & Reset Password (Admin Only)
│   │       └── settings.vue                    # Pengaturan Profil Toko, Logo & Footer Struk (Admin Only)
│   └── nuxt.config.ts                          # Konfigurasi Nuxt & Midtrans SDK
└── README.md
```

---

## 🔑 Akun Default Sistem

Setelah menjalankan seeder database, Anda dapat langsung login menggunakan akun default berikut:

| Role | Email | Password | Hak Akses Utama |
|---|---|---|---|
| **Administrator** | `admin@gmail.com` | `admin123` | Dashboard Statistik, Kelola Pengguna, Activity User, Pengaturan Profil Toko & Struk, Kelola Barang, Riwayat Penjualan, Void Transaksi |
| **Staff Kasir** | `kasir@gmail.com` | `kasir123` | Halaman Kasir POS, Kelola Inventori Barang, Riwayat Transaksi & Cetak Struk |

---

## 🚀 Panduan Menjalankan Aplikasi

### 1. Setup Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Konfigurasikan database MySQL di .env, lalu:
php artisan migrate --seed

# Jalankan server API:
php artisan serve
```

### 2. Setup Frontend (Nuxt 4)

```bash
cd frontend
npm install

# Jalankan server development:
npm run dev
```
Akses aplikasi melalui browser di: `http://localhost:3000`

---

## 📡 Ringkasan Endpoint API

### Autentikasi & Profil Toko (Publik / Auth)
- `POST /api/login` — Autentikasi pengguna & pengembalian token Sanctum serta data role.
- `POST /api/logout` — Pencabutan token & pencatatan log logout.
- `GET /api/user` — Mendapatkan profil user yang sedang aktif.
- `GET /api/setting` — Mendapatkan profil toko (nama toko, alamat, telepon, logo, footer struk).

### Manajemen Kasir & Barang (Staff Kasir & Admin)
- `GET /api/barang` — Daftar katalog produk.
- `POST /api/barang` — Tambah produk baru.
- `POST /api/barang/bulk` — Input massal / import spreadsheet.
- `PUT /api/barang/{id}` — Update data produk.
- `POST /api/barang/{id}/tambah-stok` — Tambah stok barang masuk.
- `DELETE /api/barang/{id}` — Hapus produk dari inventori (Admin).
- `GET /api/transaksi` — Riwayat transaksi penjualan.
- `POST /api/transaksi` — Pembuatan transaksi kasir baru.
- `POST /api/payment/snap` — Pembuatan token Midtrans Snap.
- `POST /api/payment/batal-snap` — Pembatalan transaksi Midtrans.
- `POST /api/print` — Perintah cetak struk ESC/POS thermal printer.

### Administrasi (Khusus Admin - Middleware `admin`)
- `GET /api/dashboard/stats` — Statistik ringkasan toko (omzet, transaksi, stok kritis, produk terlaris, grafik, dan metode pembayaran).
- `POST /api/setting` — Memperbarui profil toko, alamat, telepon, logo Base64, dan footer struk.
- `POST /api/transaksi/{id}/batal` — Membatalkan transaksi & mengembalikan stok barang.
- `DELETE /api/transaksi/{id}` — Hapus arsip transaksi.
- `GET /api/activity-logs` — Mengambil daftar log linimasa aktivitas pengguna.
- `DELETE /api/activity-logs` — Membersihkan seluruh log aktivitas.
- `GET /api/users` — Daftar seluruh akun pengguna.
- `POST /api/users` — Membuat akun pengguna baru.
- `PUT /api/users/{id}` — Mengedit nama, email, dan mengubah role.
- `POST /api/users/{id}/reset-password` — Mereset password akun pengguna.
- `DELETE /api/users/{id}` — Menghapus akun pengguna.

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan operasional point of sales dan bebas dikembangkan lebih lanjut.

# 🛒 Sistem Kasir Modern (POS - Point of Sale)

Aplikasi **Sistem Kasir (Point of Sale)** berbasis web yang modern, cepat, dan responsif dengan sistem **Multi-Role (Admin & Kasir)**. Dibangun menggunakan arsitektur terpisah (*decoupled architecture*): **Laravel** sebagai backend RESTful API yang aman (Sanctum) dan **Nuxt 4 / Vue 3 + Tailwind CSS** sebagai antarmuka pengguna yang interaktif.

---

## 🌟 Fitur Berdasarkan Peran (Role-Based Access)

Aplikasi memisahkan antarmuka dan hak akses secara ketat antara **Administrator** dan **Staff Kasir**:

| Fitur / Halaman | Staff Kasir | Administrator |
|---|:---:|:---:|
| 🛒 **Halaman Kasir (POS)** | ✅ Akses Penuh | ❌ Otomatis dialihkan ke Activity |
| 📦 **Kelola Barang & Inventori** | ✅ Akses Penuh | ❌ Disembunyikan |
| 📊 **Activity User Log** | ❌ Disembunyikan | ✅ Akses Penuh |
| 👥 **Kelola Pengguna (CRUD Users)** | ❌ Disembunyikan | ✅ Akses Penuh |
| 📜 **Riwayat Transaksi & Struk** | ✅ Akses Penuh | ✅ Akses Penuh |
| 🚫 **Batalkan (Void Transaksi)** | ❌ Disembunyikan | ✅ Diizinkan |

---

## 🚀 Fitur Lengkap

### 1. 🛒 Kasir & Transaksi POS (Khusus Staff Kasir)
- **Katalog Produk Interaktif:** Pencarian instan, filter kategori (*category chips*), dan pengurutan (nama, harga, stok).
- **Auto-Hide Out of Stock:** Produk dengan stok habis otomatis disembunyikan dari katalog transaksi kasir.
- **Kalkulasi Otomatis:** Perhitungan subtotal, diskon, pajak, dan nominal uang kembalian instan.
- **Multi-Metode Pembayaran:**
  - **Tunai (Cash)** dengan kalkulator pecahan cepat.
  - **Digital Payment Gateway (Midtrans Snap):** QRIS (GoPay, ShopeePay, OVO), Virtual Account Bank (BCA, BNI, BRI, Mandiri), dan Kartu Kredit.
- **Cetak Struk Thermal:** Integrasi pencetakan ke printer thermal ESC/POS 58mm/80mm dan cetak browser.

### 2. 📦 Manajemen Inventori & Barang (Khusus Staff Kasir)
- **CRUD Produk:** Tambah, edit, dan hapus barang lengkap dengan barcode, gambar, kategori, harga modal (HPP), harga jual, dan stok.
- **Penyesuaian Stok Cepat:** Tombol tambah stok masuk tanpa perlu edit seluruh data barang.
- **Bulk Add & Import Massal:** Form input dinamis banyak barang sekaligus serta import massal via file CSV/Spreadsheet.

### 3. 📊 Activity User Log (Khusus Administrator)
- **Pemantauan Linimasa Aktivitas:** Mencatat setiap aktivitas penting sistem secara real-time:
  - 🔐 Login & Logout pengguna (beserta alamat IP client).
  - 🛒 Pembuatan transaksi POS baru beserta nominal dan metode bayar.
  - 🚫 Pembatalan (void) transaksi beserta restock produk otomatis.
  - 📦 Penambahan, perubahan stok, dan penghapusan barang.
  - 👥 Penambahan akun, perubahan role, dan reset password.
- **Filter & Statistik:** Ringkasan total aktivitas, aktivitas hari ini, filter berdasarkan role, kategori aksi, dan tanggal.

### 4. 👥 Manajemen Pengguna (Khusus Administrator)
- **Kelola Akun:** Melihat daftar semua pengguna sistem, menambah akun baru, mengedit profil, dan mengganti role (*Admin / Kasir*).
- **Reset Password:** Mengubah password akun staf kasir secara langsung.
- **Proteksi Akun:** Mencegah administrator menghapus atau menurunkan (*demote*) role akunnya sendiri yang sedang aktif.

### 5. 📜 Riwayat Transaksi & Pelaporan
- Pencatatan seluruh transaksi penjualan lengkap dengan status (*Pending, Lunas, Dibatalkan*).
- Filter transaksi berdasarkan rentang tanggal, status, dan metode pembayaran.
- Cetak ulang struk transaksi dan pembatalan (*void*) dengan pengembalian stok otomatis.

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
│   │   │   │   ├── AuthController.php      # Login, Logout, Info User & Activity Log
│   │   │   │   ├── BarangController.php    # CRUD Barang & Bulk Import
│   │   │   │   ├── TransaksiController.php # Transaksi POS, Void & Restock
│   │   │   │   ├── PaymentController.php   # Midtrans Snap & Webhook Callback
│   │   │   │   ├── UserController.php      # CRUD Pengguna & Reset Password
│   │   │   │   ├── ActivityController.php  # Pemantauan Log Aktivitas User
│   │   │   │   └── PrintController.php     # Cetak Struk ESC/POS Printer Thermal
│   │   │   └── Middleware/
│   │   │       └── EnsureUserIsAdmin.php   # Middleware Proteksi Role Admin
│   │   └── Models/
│   │       ├── User.php                    # Model User (Role: Admin / Kasir)
│   │       ├── Barang.php                  # Model Produk & Stok
│   │       ├── Transaksi.php               # Model Penjualan & Pembayaran
│   │       └── ActivityLog.php             # Model Pencatatan Log Aktivitas
│   ├── database/
│   │   ├── migrations/                     # Migrasi tabel users, barangs, transaksis, activity_logs
│   │   └── seeders/
│   │       ├── UserSeeder.php              # Akun default admin & kasir
│   │       ├── ActivityLogSeeder.php       # Contoh awal riwayat aktivitas
│   │       └── DatabaseSeeder.php
│   ├── routes/api.php                      # Endpoint API RESTful
│   └── Dockerfile
├── frontend/
│   ├── app/
│   │   ├── composables/
│   │   │   ├── useAuth.js                  # State management user, role, token & helper isAdmin/isKasir
│   │   │   └── useToast.js                 # Notifikasi toast global
│   │   ├── layouts/
│   │   │   └── default.vue                 # Header navbar responsif dengan filter menu role
│   │   └── pages/
│   │       ├── index.vue                   # Login dengan routing otomatis sesuai role
│   │       ├── kasir.vue                   # Antarmuka Transaksi POS (Kasir Only)
│   │       ├── barang.vue                  # Manajemen Katalog & Stok (Kasir Only)
│   │       ├── riwayat.vue                 # Riwayat Penjualan & Cetak Struk (Kasir & Admin)
│   │       ├── activity.vue                # Linimasa Activity User (Admin Only)
│   │       └── users.vue                   # Kelola Pengguna & Reset Password (Admin Only)
│   └── nuxt.config.ts                      # Konfigurasi Nuxt & Midtrans SDK
└── README.md
```

---

## 🔑 Akun Default Sistem

Setelah menjalankan seeder database, Anda dapat langsung login menggunakan akun default berikut:

| Role | Email | Password | Hak Akses Utama |
|---|---|---|---|
| **Administrator** | `admin@gmail.com` | `admin123` | Activity User, Riwayat Penjualan, Kelola Pengguna, Void Transaksi |
| **Staff Kasir** | `kasir@gmail.com` | `kasir123` | Halaman Kasir POS, Kelola Inventori Barang, Riwayat Transaksi |

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

### Autentikasi
- `POST /api/login` — Autentikasi pengguna & pengembalian token Sanctum serta data role.
- `POST /api/logout` — Pencabutan token & pencatatan log logout.
- `GET /api/user` — Mendapatkan profil user yang sedang aktif.

### Manajemen Kasir & Barang (Staff Kasir)
- `GET /api/barang` — Daftar katalog produk.
- `POST /api/barang` — Tambah produk baru.
- `POST /api/barang/bulk` — Input massal / import spreadsheet.
- `PUT /api/barang/{id}` — Update data produk.
- `POST /api/barang/{id}/tambah-stok` — Tambah stok barang masuk.
- `DELETE /api/barang/{id}` — Hapus produk dari inventori.
- `GET /api/transaksi` — Riwayat transaksi penjualan.
- `POST /api/transaksi` — Pembuatan transaksi kasir baru.
- `POST /api/payment/snap` — Pembuatan token Midtrans Snap.
- `POST /api/print` — Perintah cetak struk ESC/POS.

### Administrasi (Khusus Admin - Middleware `admin`)
- `POST /api/transaksi/{id}/batal` — Membatalkan transaksi & mengembalikan stok.
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

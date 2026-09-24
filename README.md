# 🛒 Sistem Kasir Modern (POS - Point of Sale)

Aplikasi **Sistem Kasir (Point of Sale)** berbasis web yang modern, cepat, dan responsif. Dibangun dengan arsitektur terpisah (*decoupled architecture*) menggunakan **Laravel** sebagai backend RESTful API dan **Nuxt 4 / Vue 3 + Tailwind CSS** sebagai frontend antarmuka kasir yang interaktif.

---

## 🌟 Fitur Utama

### 1. 🖥️ Kasir & Transaksi Cepat (POS)
- **Katalog Produk Interaktif:** Pencarian barang instan (nama/barcode), filter kategori (*category chips*), dan sorting (nama, harga, stok).
- **Auto-Hide Out of Stock:** Barang dengan stok habis otomatis disembunyikan dari katalog kasir untuk mencegah salah input.
- **Keranjang Belanja Real-time:** Perhitungan otomatis subtotal, diskon, pajak, dan nominal kembalian.
- **Multi-Metode Pembayaran:**
  - Pembayaran **Tunai (Cash)** dengan kalkulator kembalian instan.
  - Pembayaran **Digital / Non-Tunai** via **Midtrans Payment Gateway** (QRIS, GoPay, ShopeePay, Virtual Account Bank, Kartu Kredit).
- **Cetak Struk Thermal:** Integrasi cetak struk via printer thermal (ESC/POS) dan cetak browser.

### 2. 📦 Manajemen Inventori & Barang
- **CRUD Produk:** Tambah, edit, dan hapus barang lengkap dengan foto, barcode, kategori, harga beli, dan harga jual.
- **Quick Stock Adjustment:** Tombol cepat untuk menambah stok barang masuk tanpa perlu edit keseluruhan data.
- **Bulk Add & Import:**
  - Input banyak barang sekaligus dalam satu form dinamis.
  - Import data produk massal dari file CSV / spreadsheet.

### 3. 📜 Riwayat Transaksi & Laporan
- Riwayat transaksi penjualan lengkap dengan status pembayaran (*Pending, Lunas, Dibatalkan*).
- Filter transaksi berdasarkan rentang tanggal dan status.
- Cetak ulang struk transaksi kapan saja.
- Fitur pembatalan transaksi dengan pengembalian stok otomatis.

### 4. 🔒 Keamanan & Autentikasi
- Proteksi endpoint API dengan **Laravel Sanctum (Token-based Auth)**.
- Public Webhook Midtrans yang aman untuk sinkronisasi status pembayaran otomatis.

---

## 🛠️ Tech Stack

| Bagian | Teknologi |
|---|---|
| **Frontend** | [Nuxt 4](https://nuxt.com/) (Vue 3, Vite, Composition API), [Tailwind CSS](https://tailwindcss.com/) |
| **Backend** | [Laravel 12/13](https://laravel.com/) (PHP 8.2+ / 8.3+), [Laravel Sanctum](https://laravel.com/docs/sanctum) |
| **Database** | MySQL / SQLite |
| **Payment Gateway** | [Midtrans Snap API & Webhook](https://midtrans.com/) |
| **Printer Thermal** | `mike42/escpos-php` |
| **Deployment / Container** | Docker & Laragon |

---

## 📁 Struktur Direktori

```text
Sistem-kasir/
├── backend/                  # RESTful API Backend (Laravel)
│   ├── app/
│   │   ├── Http/Controllers/ # Auth, Barang, Transaksi, Payment, Print
│   │   └── Models/          # Eloquent Models (Barang, Transaksi, DetailTransaksi)
│   ├── config/               # Konfigurasi aplikasi, CORS, auth
│   ├── database/             # Migrasi & Seeder
│   ├── routes/api.php        # Definisi rute API
│   ├── Dockerfile            # Konfigurasi Docker untuk deploy backend
│   └── docker-entrypoint.sh  # Script startup Docker
├── frontend/                 # Client Frontend (Nuxt 4 + Tailwind CSS)
│   ├── app/
│   │   └── pages/
│   │       ├── index.vue     # Halaman Login
│   │       ├── kasir.vue     # Antarmuka Utama Kasir POS
│   │       ├── barang.vue    # Manajemen Katalog & Stok Produk
│   │       └── riwayat.vue   # Riwayat Transaksi & Cetak Struk
│   └── nuxt.config.ts        # Konfigurasi Nuxt & Midtrans SDK
└── README.md                 # Dokumentasi Project
```

---

## 🚀 Panduan Instalasi & Menjalankan

### Prasyarat
- **PHP** >= 8.2 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `curl`)
- **Composer**
- **Node.js** >= 18.x & **npm**
- **MySQL** (atau SQLite)

---

### 1. Setup Backend (Laravel)

1. Masuk ke direktori `backend`:
   ```bash
   cd backend
   ```

2. Install dependensi PHP:
   ```bash
   composer install
   ```

3. Salin file `.env` dan generate application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Konfigurasikan database dan kredensial Midtrans di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistem_kasir
   DB_USERNAME=root
   DB_PASSWORD=

   # Kredensial Sandbox Midtrans
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```

5. Jalankan migrasi dan seeder database:
   ```bash
   php artisan migrate --seed
   ```

6. Jalankan server backend:
   ```bash
   php artisan serve
   ```
   *Default API URL:* `http://localhost:8000/api` (atau via virtual host Laragon)

---

### 2. Setup Frontend (Nuxt 4)

1. Buka terminal baru dan masuk ke direktori `frontend`:
   ```bash
   cd frontend
   ```

2. Install dependensi Node.js:
   ```bash
   npm install
   ```

3. Konfigurasikan environment variable (opsional, sesuaikan URL backend):
   Buat file `.env` di folder `frontend` jika diperlukan:
   ```env
   NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
   NUXT_PUBLIC_MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxx
   ```

4. Jalankan server frontend mode development:
   ```bash
   npm run dev
   ```

5. Buka browser dan akses aplikasi kasir di:
   ```text
   http://localhost:3000
   ```

---

## 💳 Konfigurasi Pembayaran Midtrans (Sandbox)

1. Daftar akun di [Midtrans Sandbox](https://dashboard.sandbox.midtrans.com/).
2. Salin **Server Key** dan **Client Key** dari menu *Settings > Access Keys*.
3. Masukkan keys ke file `.env` di backend dan frontend.
4. Untuk pengujian webhook di local environment, gunakan tunneling tools seperti [Ngrok](https://ngrok.com/) untuk meneruskan webhook ke `http://your-domain/api/payment/webhook`.

---

## 🖨️ Konfigurasi Thermal Printer

Aplikasi menggunakan library `mike42/escpos-php` untuk komunikasi langsung dengan printer kasir.
- Pastikan printer thermal USB/Network telah terpasang dan dikenali oleh OS.
- Atur nama printer pada controller `backend/app/Http/Controllers/PrintController.php` sesuai dengan printer sharing name Anda.

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan operasional kasir dan bebas dikembangkan lebih lanjut.

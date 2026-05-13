# Sistem Informasi Pembayaran Uang Sekolah

Website manajemen pembayaran uang sekolah berbasis Laravel. Aplikasi ini memiliki backend API dan website monolith menggunakan Laravel Blade, Tailwind CSS, Alpine.js, Axios, Laravel Sanctum, dan Midtrans Snap untuk pembayaran.

## Ringkasan Teknologi

- Laravel Framework: 13.8.0
- PHP: ^8.3
- Database: MySQL atau SQLite, mengikuti konfigurasi `.env`
- Frontend: Laravel Blade, Tailwind CSS 4, Alpine.js, Axios, Vite
- Auth web: session-based guard Laravel
- Auth API: Laravel Sanctum token
- Payment gateway: Midtrans Snap
- PDF export: barryvdh/laravel-dompdf

## Fitur Utama

### Auth

- Login web menggunakan `username` dan `password`
- Logout session
- Redirect otomatis ke dashboard sesuai role
- Role tersedia: `admin`, `guru`, `orang_tua`, `siswa`

### Admin

- Dashboard ringkasan total siswa, guru, tagihan bulan ini, dan pembayaran terkumpul
- Grafik pembayaran 6 bulan terakhir
- CRUD kelas
- CRUD guru
- CRUD orang tua
- CRUD siswa
- CRUD tagihan
- Assign tagihan ke siswa
- Detail tagihan dan progress pembayaran siswa
- Riwayat seluruh pembayaran
- Laporan pembayaran dengan filter bulan, tahun, kelas
- Export laporan PDF
- Notifikasi
- Profil dan ganti password

### Guru

- Dashboard pemantauan siswa, kelas, dan status pembayaran
- Data siswa read-only
- Detail siswa read-only
- Data tagihan read-only
- Detail tagihan dan status pembayaran siswa read-only
- Notifikasi
- Profil dan ganti password

### Orang Tua

- Dashboard tagihan anak
- Daftar tagihan anak dengan filter anak dan status
- Halaman bayar tagihan menggunakan Midtrans Snap
- Riwayat pembayaran anak
- Notifikasi
- Profil dan ganti password

### Siswa

- Dashboard tagihan aktif bulan ini
- Daftar tagihan siswa
- Riwayat pembayaran siswa
- Notifikasi
- Profil dan ganti password

## Struktur Penting

```text
app/Http/Controllers/Web
+-- Auth/LoginController.php
+-- Admin/*
+-- Guru/*
+-- OrangTua/*
+-- Siswa/*
+-- Concerns/*
+-- ProfilController.php

resources/views
+-- layouts/app.blade.php
+-- auth/login.blade.php
+-- admin/*
+-- guru/*
+-- orang-tua/*
+-- siswa/*
+-- notifikasi/*
+-- profil/*
+-- components/*

routes
+-- web.php
+-- api.php

database
+-- migrations/*
+-- seeders/*
```

## Instalasi

Pastikan sudah tersedia PHP 8.3+, Composer, Node.js, npm, dan database.

1. Clone atau buka folder project.

```bash
cd Proyek_pembayaran_sekolah
```

2. Install dependency PHP.

```bash
composer install
```

3. Install dependency frontend.

```bash
npm install
```

4. Buat file environment.

```bash
cp .env.example .env
```

Di Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

5. Generate app key.

```bash
php artisan key:generate
```

6. Atur koneksi database di `.env`.

Contoh MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_pembayaran_sekolah
DB_USERNAME=root
DB_PASSWORD=
```

7. Jalankan migration dan seeder.

```bash
php artisan migrate --seed
```

Jika database sudah ada dan ingin reset ulang:

```bash
php artisan migrate:fresh --seed
```

8. Build asset frontend.

```bash
npm run build
```

9. Jalankan server.

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

Untuk mode development frontend:

```bash
npm run dev
```

## Konfigurasi Environment

Konfigurasi penting di `.env`:

```env
APP_NAME="Sistem Pembayaran Sekolah"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

Untuk Midtrans sandbox:

- Gunakan `MIDTRANS_IS_PRODUCTION=false`
- Isi `MIDTRANS_SERVER_KEY` dari dashboard Midtrans sandbox
- Isi `MIDTRANS_CLIENT_KEY` dari dashboard Midtrans sandbox

## Akun Demo Seeder

Seeder menyediakan akun awal berikut:

| Role | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `admin123` |
| Guru | `guru1` | `guru123` |
| Orang Tua | `ortu1` | `ortu123` |
| Siswa | `siswa1` | `siswa123` |

Seeder juga membuat data profil dasar guru, orang tua, siswa, dan kelas.

## Route Web Utama

### Public

- `GET /login`
- `POST /login`
- `POST /logout`

### Admin

- `/admin/dashboard`
- `/admin/kelas`
- `/admin/guru`
- `/admin/orang-tua`
- `/admin/siswa`
- `/admin/tagihan`
- `/admin/pembayaran`
- `/admin/laporan`
- `/admin/notifikasi`
- `/admin/profil`

### Guru

- `/guru/dashboard`
- `/guru/siswa`
- `/guru/tagihan`
- `/guru/notifikasi`
- `/guru/profil`

### Orang Tua

- `/orang-tua/dashboard`
- `/orang-tua/tagihan`
- `/orang-tua/tagihan/{tagihanSiswa}/bayar`
- `/orang-tua/pembayaran`
- `/orang-tua/notifikasi`
- `/orang-tua/profil`

### Siswa

- `/siswa/dashboard`
- `/siswa/tagihan`
- `/siswa/pembayaran`
- `/siswa/notifikasi`
- `/siswa/profil`

## API Singkat

API berada di `routes/api.php` dan memakai Laravel Sanctum.

- `POST /api/login`
- `POST /api/logout`
- `GET /api/me`
- Resource API untuk kelas, guru, orang tua, siswa, dan tagihan

API membutuhkan token Sanctum untuk route protected.

## Alur Pembayaran Midtrans

1. Orang tua membuka `/orang-tua/tagihan`.
2. Pilih tagihan yang belum lunas.
3. Klik `Bayar Sekarang`.
4. Halaman bayar memanggil endpoint:

```text
POST /orang-tua/tagihan/{tagihanSiswa}/snap-token
```

5. Server membuat Snap token lewat Midtrans PHP SDK.
6. Frontend memanggil `window.snap.pay(token)`.
7. Status awal transaksi disimpan sebagai `pending`.

Catatan: webhook/callback server-to-server Midtrans belum didokumentasikan sebagai endpoint khusus di project ini. Status final pembayaran masih mengikuti data transaksi yang tersimpan dan callback frontend.

## Command Development

Build frontend production:

```bash
npm run build
```

Jalankan Vite development:

```bash
npm run dev
```

Jalankan test:

```bash
php artisan test
```

Compile cache Blade:

```bash
php artisan view:cache
```

Lihat route:

```bash
php artisan route:list
```

Clear cache umum:

```bash
php artisan optimize:clear
```

## Database Utama

Tabel utama:

- `users`
- `kelas`
- `gurus`
- `orang_tuas`
- `siswas`
- `tagihans`
- `tagihan_siswas`
- `pembayarans`
- `notifikasis`
- `sessions`

Relasi penting:

- User memiliki satu profil sesuai role: guru, siswa, atau orang tua
- Orang tua memiliki banyak siswa
- Siswa berada di satu kelas
- Tagihan di-assign ke siswa melalui `tagihan_siswas`
- Pembayaran terhubung ke satu `tagihan_siswa`
- Notifikasi terhubung ke user

## Catatan Operasional

- Semua form web menggunakan CSRF protection.
- Web menggunakan session auth, bukan token.
- Dashboard dan halaman tiap role dilindungi middleware `auth.web` dan `role`.
- Badge notifikasi di header melakukan polling unread count setiap 30 detik.
- File asset publik berada di `public/`, termasuk logo di `public/images/logo.jpeg`.
- Untuk production, set `APP_DEBUG=false`, isi `APP_URL`, dan gunakan key Midtrans production.

## Troubleshooting

### Login berhasil tetapi masuk dashboard 404

Pastikan user memiliki profil sesuai role. Seeder terbaru sudah membuat profil dasar. Untuk data lama, controller orang tua otomatis membuat profil minimal jika belum ada.

### Midtrans gagal membuat pembayaran

Periksa:

- `MIDTRANS_SERVER_KEY`
- `MIDTRANS_CLIENT_KEY`
- `MIDTRANS_IS_PRODUCTION`
- nominal tagihan harus lebih dari 0
- koneksi internet server ke Midtrans

Setelah mengubah `.env`, jalankan:

```bash
php artisan optimize:clear
```

### Asset tidak berubah

Jalankan ulang:

```bash
npm run build
php artisan view:clear
```

Lalu hard refresh browser.

## Status Verifikasi Terakhir

Perintah yang sudah digunakan untuk verifikasi:

```bash
php artisan view:cache
php artisan test
npm run build
```

Semua berhasil pada kondisi terakhir pengembangan.

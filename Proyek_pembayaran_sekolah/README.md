# Sistem Informasi Pembayaran Uang Sekolah

Website manajemen pembayaran uang sekolah berbasis Laravel. Aplikasi ini memiliki backend API dan website monolith menggunakan Laravel Blade, Tailwind CSS, Alpine.js, Axios, Laravel Sanctum, dan pembayaran transfer bank manual dengan upload bukti.

## Ringkasan Teknologi

- Laravel Framework: 13.8.0
- PHP: ^8.3
- Database: MySQL atau SQLite, mengikuti konfigurasi `.env`
- Frontend: Laravel Blade, Tailwind CSS 4, Alpine.js, Axios, Vite
- Auth web: session-based guard Laravel
- Auth API: Laravel Sanctum token
- Pembayaran: transfer bank manual dan verifikasi admin
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
- Halaman bayar tagihan menggunakan transfer bank dan upload bukti pembayaran
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

PAYMENT_BANK_NAME="BANK BRI"
PAYMENT_ACCOUNT_NUMBER=011301002289308
PAYMENT_ACCOUNT_HOLDER="SMK 1 GKPI"

GOWA_API_URL=http://127.0.0.1:3000
GOWA_USERNAME=your_gowa_username
GOWA_PASSWORD=your_strong_gowa_password
GOWA_DEVICE_ID=
GOWA_TIMEOUT=15
```

Konfigurasi rekening pembayaran:

- `PAYMENT_BANK_NAME` berisi nama bank tujuan transfer.
- `PAYMENT_ACCOUNT_NUMBER` berisi nomor rekening tujuan transfer.
- `PAYMENT_ACCOUNT_HOLDER` berisi nama pemilik rekening.

Nilai `GOWA_USERNAME` dan `GOWA_PASSWORD` harus sama dengan salah satu akun pada
`APP_BASIC_AUTH` di service GOWA. `GOWA_DEVICE_ID` boleh dikosongkan jika hanya
ada satu perangkat WhatsApp yang terdaftar.

## Notifikasi WhatsApp GOWA

GOWA berjalan sebagai service terpisah dari Laravel. Laravel mengirim pesan ke:

```text
POST {GOWA_API_URL}/send/message
```

Notifikasi WhatsApp dikirim melalui queue untuk:

- tagihan baru setelah tagihan di-assign ke siswa
- pembayaran yang berubah menjadi `lunas`
- pengingat manual untuk tagihan berstatus `belum_bayar`

Jalankan queue worker bersama server Laravel:

```bash
php artisan queue:work --tries=3 --timeout=60 --sleep=3
```

Route admin yang tersedia:

- `POST /admin/tagihan/{tagihan}/blast-pengingat`
- `POST /admin/tagihan-siswa/{tagihanSiswa}/notifikasi`
- `GET /admin/wa/test?phone=628xxxxxxxx`

Pesan yang berhasil dikirim juga disimpan ke tabel `notifikasis`. Periksa
`storage/logs/laravel.log` dan `php artisan queue:failed` jika pengiriman gagal.

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

## Alur Pembayaran Transfer Manual

1. Orang tua membuka `/orang-tua/tagihan`.
2. Pilih tagihan yang belum lunas.
3. Klik `Bayar Sekarang`.
4. Orang tua transfer sesuai nominal ke rekening sekolah yang tampil di halaman bayar.
5. Orang tua upload bukti pembayaran melalui endpoint:

```text
POST /orang-tua/tagihan/{tagihanSiswa}/upload-bukti
```

6. Sistem menyimpan transaksi dengan status `pending`.
7. Admin membuka `/admin/pembayaran`, memeriksa bukti transfer, lalu memilih `Tandai Lunas` atau `Tolak`.
8. Jika disetujui, status tagihan menjadi `lunas`. Jika ditolak, status tagihan kembali menjadi `belum_bayar` dan orang tua dapat upload bukti ulang.

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
- Untuk production, set `APP_DEBUG=false` dan isi `APP_URL`. Bukti pembayaran disimpan di storage private dan hanya dibuka melalui route yang dilindungi login.

## Troubleshooting

### Login berhasil tetapi masuk dashboard 404

Pastikan user memiliki profil sesuai role. Seeder terbaru sudah membuat profil dasar. Untuk data lama, controller orang tua otomatis membuat profil minimal jika belum ada.

### Upload bukti pembayaran gagal

Periksa:

- file berformat JPG, PNG, atau PDF
- ukuran file maksimal 4 MB
- nominal tagihan harus lebih dari 0
- folder storage dapat ditulis aplikasi

Setelah mengubah `.env`, jalankan:

```bash
php artisan optimize:clear
```

### Status pembayaran tetap menunggu verifikasi

Pastikan:

- admin sudah membuka halaman `/admin/pembayaran`
- bukti transfer sudah sesuai nominal dan rekening tujuan
- admin sudah menekan tombol `Tandai Lunas`

Cek log jika upload atau verifikasi gagal:

```powershell
Get-Content storage/logs/laravel.log -Wait -Tail 20
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

## Checklist Deployment

Gunakan `.env.production.example` sebagai acuan dan jangan deploy file `.env`
lokal. Nilai berikut wajib diganti:

- `APP_URL`, `APP_KEY`, dan koneksi database
- konfigurasi rekening pembayaran sekolah
- akun Basic Auth GOWA yang kuat
- `GOWA_API_URL` yang dapat dijangkau server Laravel

Jika GOWA berjalan pada server yang sama, `http://127.0.0.1:3000` dapat
digunakan. Jika berbeda server, gunakan jaringan privat atau HTTPS dan jangan
membuka GOWA ke internet tanpa autentikasi.

Sebelum pertama kali menjalankan compose GOWA, buat volume persisten:

```bash
docker volume create go-whatsapp-web-multidevice_whatsapp
docker compose up -d
```

Volume tersebut menyimpan database sesi WhatsApp. Jangan menghapus volume saat
update container karena perangkat harus login ulang jika database sesi hilang.

Perintah deployment Laravel:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan queue:restart
```

Queue worker harus dijalankan permanen melalui Supervisor, systemd, atau process
manager hosting:

```bash
php artisan queue:work --tries=3 --timeout=60 --sleep=3
```

File bukti pembayaran tersimpan di storage private, sedangkan database hanya
menyimpan path file. Admin dan orang tua membuka bukti melalui route aplikasi
yang sudah melewati autentikasi.

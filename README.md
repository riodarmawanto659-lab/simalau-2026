# Simalau - Sistem Manajemen Laundry

Simalau adalah aplikasi manajemen laundry berbasis Laravel, Filament, MariaDB, Nginx, dan Docker. Web ini dibuat mengikuti BRD/PRD Sistem Manajemen Laundry dengan alur pelanggan dan admin.

## Fitur Utama

- Halaman publik sesuai wireframe: beranda, layanan, detail layanan, cek status cucian, login, dan registrasi.
- Dashboard pelanggan: buat pesanan, riwayat pesanan, detail tracking cucian, status pembayaran, dan profil.
- Panel admin Filament: pelanggan, layanan, pesanan, detail pesanan, pembayaran, riwayat status, reminder pengambilan, arus kas, pengaturan sistem, hari libur, dan user.
- Workflow operasional: nomor pesanan otomatis, antrean FIFO, status cucian berurutan, pembayaran, kas masuk otomatis, dan reminder cucian siap diambil lebih dari 3 hari.

## Akun Demo

- Admin: `admin@admin.com` / `password`
- Pelanggan: `pelanggan@example.com` / `password`

## Menjalankan dengan Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec php php artisan db:seed
```

## URL

- Website: `https://simalau.test`
- Admin Panel: `https://simalau.test/admin`

Pastikan domain lokal `simalau.test` diarahkan ke `127.0.0.1` pada hosts file.

## Stack

- PHP 8.3+
- Laravel 12
- Filament v3
- Livewire dan Blade
- MariaDB
- Nginx
- Docker

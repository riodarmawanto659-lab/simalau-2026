# Fix Midtrans ENV SIMALAU

Perbaikan ini hanya menyentuh konfigurasi Midtrans dan file environment. Desain Blade/CSS tidak diubah.

## Yang diperbaiki

1. Menambahkan `.env` pada root Docker project agar Docker Compose membaca variabel Midtrans.
2. Menambahkan `src/.env` agar Laravel langsung membaca `MIDTRANS_MERCHANT_ID`, `MIDTRANS_CLIENT_KEY`, dan `MIDTRANS_SERVER_KEY`.
3. Mengisi ulang `src/.env.example` dan `.env.example` agar saat project dicopy ulang key tetap tersedia.
4. Memperbaiki `php/docker-entrypoint.sh` karena file ini sebelumnya membuat/menimpa `/var/www/html/.env` tanpa variabel Midtrans.
5. Menambahkan `php artisan optimize:clear` pada startup container supaya cache config Laravel tidak menahan nilai lama.

## Catatan penting

`config/midtrans.php` tetap menggunakan format yang benar:

```php
'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
'client_key' => env('MIDTRANS_CLIENT_KEY'),
'server_key' => env('MIDTRANS_SERVER_KEY'),
```

Jangan diubah menjadi `env('M151431836')` atau `env('Mid-server-...')`, karena itu salah. Isi asli key harus berada di `.env`, sedangkan `config/midtrans.php` hanya membaca nama variabelnya.

## Setelah extract ZIP

Jalankan:

```bash
docker compose down
docker compose up -d --build
docker compose exec php php artisan optimize:clear
docker compose exec php php artisan tinker
```

Lalu cek:

```php
config('midtrans.server_key')
```

Jika muncul value `Mid-server-...`, tombol Bayar dengan Midtrans Sandbox tidak akan error karena `MIDTRANS_SERVER_KEY` null lagi.

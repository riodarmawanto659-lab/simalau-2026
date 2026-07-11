# Revisi Midtrans Sandbox SIMALAU

Revisi ini dibuat dari file ZIP yang dikirim dan tidak mengubah desain utama halaman. Perubahan hanya menambahkan alur pembayaran Midtrans Sandbox dan memperbaiki error `nomor_pembayaran`.

## Perubahan Utama

1. Menambahkan konfigurasi Midtrans dari `.env`:
   - `MIDTRANS_IS_PRODUCTION=false`
   - `MIDTRANS_MERCHANT_ID`
   - `MIDTRANS_CLIENT_KEY`
   - `MIDTRANS_SERVER_KEY`
2. Menambahkan kolom Midtrans pada tabel `pembayarans`:
   - `midtrans_order_id`
   - `snap_token`
   - `snap_redirect_url`
   - `transaction_status`
   - `payment_type`
   - `fraud_status`
   - `midtrans_response`
3. Memperbaiki error `Field 'nomor_pembayaran' doesn't have a default value`.
4. Menambahkan tombol `Bayar dengan Midtrans` di detail pesanan tanpa mengubah layout lama.
5. Menambahkan halaman pembayaran Midtrans dengan class desain lama (`panel`, `stat-card`, `btn`).
6. Menambahkan webhook Midtrans di `/api/midtrans/notification`.
7. Jika webhook Midtrans sukses, status pembayaran berubah menjadi `lunas` dan otomatis masuk ke `Arus Kas` sebagai `Kas Masuk`.

## File Baru

- `src/config/midtrans.php`
- `src/app/Services/MidtransPaymentService.php`
- `src/app/Http/Controllers/MidtransPaymentController.php`
- `src/resources/views/customer/payments/midtrans.blade.php`
- `src/database/migrations/2026_07_07_110000_add_midtrans_fields_to_pembayarans_table.php`

## File yang Diubah

- `src/app/Services/LaundryOrderService.php`
- `src/app/Models/Pembayaran.php`
- `src/app/Http/Controllers/CustomerDashboardController.php`
- `src/resources/views/customer/orders/show.blade.php`
- `src/routes/web.php`
- `src/routes/api.php`
- `src/.env.example`
- `.env.example`

## Jalankan Setelah Replace Project

```bash
cd simalau-2026-web
docker compose exec php bash
cd /var/www/html
php artisan migrate
php artisan optimize:clear
php artisan route:list | grep midtrans
```

Route yang harus muncul:

```text
GET|HEAD  dashboard/pembayaran/{pembayaran}/midtrans
POST      api/midtrans/notification
```

## Setup Webhook Midtrans Sandbox

Jika masih memakai local domain `https://simalau.test`, gunakan tunnel seperti ngrok:

```bash
ngrok http https://simalau.test
```

Masukkan URL webhook di dashboard Midtrans Sandbox:

```text
https://URL-NGROK-KAMU/api/midtrans/notification
```

## Catatan

Project ini tidak memakai package `midtrans/midtrans-php`, tetapi memakai HTTP client bawaan Laravel agar tidak perlu mengubah dependency/vendor. Server Key tetap hanya dibaca dari backend `.env` dan tidak ditampilkan ke frontend.

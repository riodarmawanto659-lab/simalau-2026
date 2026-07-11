# Perbaikan Error Pengingat Pengambilan

## Masalah
Error terjadi saat pelanggan membuat pesanan baru:

`Column tanggal_siap_diambil cannot be null`

Penyebabnya ada di `app/Models/Pesanan.php`: sistem langsung membuat data `pengingat_pengambilans` ketika pesanan baru dibuat. Pada pesanan baru, kolom `tanggal_siap_diambil` memang masih kosong karena cucian belum selesai dan belum berstatus `Siap Diambil`.

## Perbaikan
- Pengingat tidak lagi dibuat saat pesanan baru dibuat.
- Pengingat hanya dibuat jika pesanan sudah berstatus `siap_diambil` dan `tanggal_siap_diambil` sudah lewat minimal 3 hari.
- Ditambahkan migration pengaman agar kolom `tanggal_siap_diambil` pada tabel `pengingat_pengambilans` menjadi nullable apabila database lama masih memakai struktur `NOT NULL`.

## File yang Diubah
- `src/app/Models/Pesanan.php`
- `src/database/migrations/2026_07_06_170000_make_tanggal_siap_diambil_nullable_on_pengingat_pengambilans_table.php`

## Jalankan Setelah Replace Project

```bash
docker compose exec php bash
cd /var/www/html
php artisan migrate
php artisan optimize:clear
```

Untuk membuat pengingat yang sudah jatuh tempo:

```bash
php artisan laundry:generate-pengingat-pengambilan
```

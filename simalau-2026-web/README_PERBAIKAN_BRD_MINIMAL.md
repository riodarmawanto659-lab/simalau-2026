# Perbaikan Minimal Sesuai BRD/PRD

Revisi ini dibuat dari ZIP lama `simalau-2026-web-final.zip` dengan prinsip: **tidak mengubah desain utama yang sudah ada**. Perubahan hanya menambahkan kebutuhan fitur dan memperbaiki alur proses.

## Yang Ditambahkan

1. **Gambar Layanan dari Admin**
   - Tabel `layanan_laundries` mendapat kolom `gambar`.
   - Admin dapat upload gambar dari menu **Layanan Laundry**.
   - Gambar tampil di halaman daftar layanan, detail layanan, dan pilihan layanan saat pelanggan membuat pesanan.
   - Jika admin belum upload gambar, desain lama tetap tampil menggunakan icon huruf seperti sebelumnya.

2. **Gambar Detail Pesanan**
   - Tabel `detail_pesanans` mendapat kolom `gambar_item`.
   - Admin dapat upload foto item/cucian dari menu **Detail Pesanan**.
   - Gambar tampil di halaman detail pesanan pelanggan jika tersedia.

3. **Halaman Kontak Terpisah**
   - Route baru: `/kontak`.
   - Menu Kontak di navbar tidak lagi menuju anchor beranda, tetapi membuka halaman kontak sendiri.
   - Halaman kontak menampilkan WhatsApp, email, jam operasional, alamat, status hari libur aktif, dan jadwal hari libur mendatang.

4. **Pembayaran Sinkron ke Arus Kas**
   - Saat pembayaran lunas disimpan, data pembayaran otomatis membuat/memperbarui **Kas Masuk** pada tabel `arus_kas`.
   - Jika pembayaran belum lunas, kas masuk otomatis terkait pembayaran tersebut dihapus agar data tidak salah.
   - Kas Keluar tetap dicatat manual melalui menu **Arus Kas**.

5. **Pengingat Pengambilan**
   - Command baru: `php artisan laundry:generate-pengingat-pengambilan`.
   - Command ini membuat pengingat untuk pesanan berstatus `siap_diambil` yang belum diambil minimal 3 hari.
   - Scheduler disiapkan agar command berjalan setiap jam saat scheduler Laravel aktif.
   - Admin mendapatkan tombol **Hubungi WA** dan **Tandai Dihubungi** di menu Pengingat Pengambilan.

6. **Informasi Hari Libur**
   - Hari libur aktif dari admin tampil di halaman kontak.
   - Saat pelanggan membuat pesanan, sistem menampilkan pemberitahuan jika hari ini sedang libur.

## Perintah Setelah Replace Project

Masuk ke container PHP:

```bash
docker compose exec php bash
cd /var/www/html
```

Jalankan:

```bash
php artisan migrate
php artisan storage:link
php artisan optimize:clear
php artisan laundry:generate-pengingat-pengambilan
```

Jika menggunakan scheduler otomatis di server, pastikan scheduler Laravel berjalan.

## Catatan

Desain utama, warna, layout, sidebar, card, dan struktur halaman lama dipertahankan. Perubahan visual hanya muncul ketika data baru seperti gambar layanan, gambar item, atau informasi hari libur tersedia.

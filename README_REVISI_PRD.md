# Revisi PRD Sistem Manajemen Laundry

Perubahan utama pada ZIP revisi ini:

1. **Beranda dirapikan**
   - Route `/` diarahkan ke halaman beranda baru.
   - Tampilan hero, layanan unggulan, cara kerja, dan CTA dibuat lebih rapi.

2. **Halaman Kontak dibuat page sendiri**
   - Route `/kontak` sudah menjadi halaman mandiri.
   - Menampilkan WhatsApp, email, alamat, jam operasional, dan informasi hari libur.

3. **Gambar layanan dari admin**
   - Ditambahkan field `gambar` pada tabel `layanan_laundries`.
   - Admin dapat upload gambar layanan lewat Filament Resource `Layanan Laundry`.
   - Gambar tampil di beranda, daftar layanan, dan detail layanan.

4. **Gambar pada detail pesanan**
   - Ditambahkan field `gambar_item` pada tabel `detail_pesanans`.
   - Admin dapat upload gambar item/cucian lewat Filament Resource `Detail Pesanan`.
   - Jika gambar item kosong, detail pesanan akan memakai gambar layanan.

5. **Pembayaran otomatis masuk Arus Kas**
   - Ditambahkan `PembayaranObserver`.
   - Saat pembayaran berstatus `lunas`, sistem otomatis:
     - update status pembayaran pesanan,
     - membuat/memperbarui arus kas jenis `masuk`.
   - Jika pembayaran kembali `belum_dibayar`, arus kas terkait dihapus.

6. **Pengingat pengambilan otomatis**
   - Ditambahkan `GeneratePengingatPengambilan` command.
   - Scheduler menjalankan pengecekan setiap jam.
   - Pesanan `siap_diambil` yang belum diambil minimal 3 hari otomatis masuk ke menu `Pengingat Pengambilan`.
   - Admin bisa membuka WhatsApp pelanggan dari tabel pengingat.

7. **Hari libur tampil ke pelanggan**
   - Informasi hari libur aktif muncul pada layout publik, halaman kontak, dan halaman buat pesanan.

8. **Card halaman buat pesanan dibenahi**
   - Route `/dashboard/pesanan/buat` dibuat ulang.
   - Card layanan, detail cucian, metode penyerahan, dan ringkasan dibuat lebih rapi dan responsif.

## Command setelah extract ZIP

Masuk ke container PHP:

```bash
docker compose exec php bash
```

Jalankan migration:

```bash
php artisan migrate
```

Buat storage link agar upload gambar tampil:

```bash
php artisan storage:link
```

Clear cache:

```bash
php artisan optimize:clear
```

Opsional, jalankan pengingat manual:

```bash
php artisan laundry:generate-pengingat-pengambilan
```

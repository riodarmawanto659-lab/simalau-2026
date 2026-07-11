# Midtrans Sandbox Final Fix

Perbaikan ini fokus ke error:

`MIDTRANS_SERVER_KEY belum diset di file .env.`

File yang diperbaiki:

- `src/config/midtrans.php`
- `src/.env`
- `src/.env.example`
- `.env`
- `.env.example`
- `docker-compose.yml`
- `php/docker-entrypoint.sh`
- `src/app/Services/MidtransPaymentService.php`
- `src/app/Http/Controllers/MidtransPaymentController.php`

Setelah extract ZIP, jalankan:

```bash
docker compose down
docker compose up -d --build
docker compose exec php php artisan optimize:clear
```

Cek value config:

```bash
docker compose exec php php artisan tinker
```

Lalu di tinker:

```php
config('midtrans.server_key')
config('midtrans.client_key')
```

Keduanya harus tidak `null`.

# Concert District

Aplikasi Laravel untuk katalog konser, reservasi tiket, pembayaran simulasi, dan validasi gate. Satu repo berisi REST API di `/api/v1` dan UI web server-rendered (Blade, Tailwind, Alpine.js) untuk admin dan penonton.

## Kebutuhan

- PHP 8.5 (minimal 8.2) dengan ekstensi `pdo_pgsql`, `gd`, `mbstring`, `openssl`, `curl`, dan `fileinfo`
- Composer
- Node.js dan npm
- Docker Desktop atau PostgreSQL 16 dan Mailpit lokal

## Menjalankan lokal

1. Pasang dependensi:

```bash
composer install
npm install
```

2. Siapkan environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Nyalakan PostgreSQL dan Mailpit:

```bash
docker compose up -d postgres mailpit
```

Konfigurasi database contoh di `.env.example` sudah cocok dengan `docker-compose.yml`.

4. Isi `.env`:

```
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=Password123
SEED_DEMO_DATA=true
```

`SEED_DEMO_DATA=true` mengisi beberapa venue, artis, event, dan kategori tiket contoh. Kredensial Cloudinary (`CLOUDINARY_*`) hanya diperlukan jika ingin upload gambar.

5. Buat tabel dan akun admin:

```bash
php artisan migrate --seed
```

6. Jalankan aplikasi:

```bash
npm run dev
```

Perintah ini menjalankan Vite, `php artisan serve`, dan `php artisan schedule:work` sekaligus. Buka aplikasi di `http://localhost:8000` dan Mailpit di `http://localhost:8025`.

Untuk UI tanpa mode dev, jalankan `npm run build` lalu `php artisan serve`.

## Akun demo

- Admin: email dari `ADMIN_EMAIL`, password dari `ADMIN_PASSWORD`, dibuat saat seeder. Admin diarahkan ke `/admin`.
- Pelanggan: daftar lewat `/register`, lalu buka Mailpit di `http://localhost:8025` untuk mengambil kode OTP 6 digit. Belum ada akun pelanggan bawaan.

## API

Base URL API: `/api/v1`. Semua response memakai bentuk `status`, `message`, dan `data`. Daftar memakai `data.items` dan `data.meta` (`current_page`, `last_page`, `per_page`, `total`).

- Auth: `POST /auth/register`, `POST /auth/verify-otp`, `POST /auth/resend-otp`, `POST /auth/login`, `GET /auth/me`, `POST /auth/logout`.
- Katalog publik: `GET /venues`, `GET /venues/{id}`, `GET /artists`, `GET /artists/{id}`, `GET /events`, `GET /events/{slug}`, `GET /ticket-categories`.
- Katalog admin (token Sanctum, role admin): create, update, delete untuk venue, artist, event, dan kategori tiket. Event admin memakai id lewat `GET /admin/events/{id}`.
- Order dan tiket (token Sanctum, user terverifikasi): `GET /orders`, `POST /orders`, `GET /orders/{id}`, `PATCH /orders/{id}/cancel`, `POST /orders/{id}/pay`, `GET /tickets`.
- Gate (token admin): `POST /tickets/validate` dengan `ticket_code`.

Contoh cepat:

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@example.com","password":"Password123"}'
```

## Scheduler

Order PENDING otomatis dibatalkan setelah `ORDER_TTL_MINUTES` (default 60) oleh command `orders:cancel-expired`, dijadwalkan setiap menit. Di production, jalankan cron:

```cron
* * * * * cd /path/to/concert-district && php artisan schedule:run >> /dev/null 2>&1
```

## Catatan

Pembayaran masih simulasi. QR tiket dibuat saat halaman dibuka, tidak disimpan sebagai file. Lihat [DEPLOY.md](DEPLOY.md) untuk checklist production.

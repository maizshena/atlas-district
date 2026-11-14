# Checklist deploy

## Konfigurasi aplikasi

- Set `APP_ENV=production` dan `APP_DEBUG=false`.
- Set `APP_URL`, lalu buat `APP_KEY` unik dan rahasiakan.
- Isi kredensial PostgreSQL pada `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
- Isi `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, dan `CLOUDINARY_API_SECRET`. Tanpa nilai ini upload gambar ditolak dengan pesan validasi; CRUD tetap berjalan tanpa gambar.
- Isi kredensial SMTP atau Resend. Jangan gunakan Mailpit di production.
- Set `ADMIN_EMAIL` dan `ADMIN_PASSWORD` sebelum menjalankan `php artisan db:seed --force`. Seeder admin menolak jalan jika keduanya kosong.
- Biarkan `SEED_DEMO_DATA=false` di production, kecuali memang ingin data contoh.
- Set `ORDER_TTL_MINUTES`, `OTP_TTL_MINUTES`, dan `MAX_TICKETS_PER_ORDER` sesuai kebutuhan.
- Pastikan timezone `APP_TIMEZONE=Asia/Jakarta`.

## Build dan rilis

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Jangan jalankan `migrate:fresh` di production. Jalankan `php artisan db:seed --force` satu kali setelah migrate pertama untuk membuat akun admin.

## Proses latar

Atur cron scheduler tiap menit:

```cron
* * * * * cd /path/to/concert-district && php artisan schedule:run >> /dev/null 2>&1
```

`QUEUE_CONNECTION=sync` cukup untuk mulai. Gunakan queue database dan worker terkelola jika pengiriman email memperlambat request. Pastikan worker tetap aktif setelah deploy.

## Session dan penyimpanan

`SESSION_DRIVER=file` hanya aman jika filesystem host persisten dan satu instance memakai file session yang sama. Pada host dengan filesystem sementara atau beberapa instance, gunakan session database atau Redis. Untuk database, buat migration sessions dengan `php artisan make:session-table --no-interaction`, deploy migration tersebut, lalu ubah `SESSION_DRIVER=database`.

## Ekstensi dan pemeriksaan

Pastikan PHP memuat `pdo_pgsql`, `gd`, `mbstring`, `openssl`, `curl`, dan `fileinfo`. Sebelum menerima trafik, verifikasi koneksi PostgreSQL, migration, SMTP, upload Cloudinary, scheduler, cache route/view, serta alur register sampai validasi gate.

## Catatan operasi

Pembayaran saat ini simulasi. Aktifkan HTTPS di reverse proxy dan pastikan `APP_URL` memakai HTTPS. Jangan simpan file `.env` atau kredensial di repository.

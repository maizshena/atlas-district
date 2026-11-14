# Concert District — agent notes

Laravel 13 app on PHP 8.5. Ticketing: events, ticket categories with quota, orders, tickets, gate validation.

## Stack

- PHP 8.5, Laravel 13.42, Sanctum 4, PHPUnit 12
- PostgreSQL 16 (Docker), Mailpit for local mail
- Blade + Alpine + Tailwind 4 via Vite
- Cloudinary for image upload, Endroid for QR codes

## Local setup

```bash
docker compose up -d          # postgres:5432, mailpit:8025
composer install
npm install
php artisan migrate --seed --force
npm run dev                   # or: composer run dev
```

Admin account comes from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`. Seeder refuses to run without them.

Mailpit UI: http://localhost:8025

## Testing

```bash
vendor/bin/phpunit --no-coverage                  # full suite
vendor/bin/phpunit --no-coverage tests/Feature/X  # one file
```

Tests use SQLite in memory (`phpunit.xml`). Queue runs `sync` in tests.

`pdo_sqlite` must be enabled in `php.ini`.

## Code layout

- `app/Services/` holds business logic. Controllers delegate.
- `app/Http/Requests/` holds validation. No inline validation in controllers.
- `app/Enums/OrderStatus.php` — never compare order status against string literals.
- `app/Support/ApiResponse.php` — uniform API envelope.
- Web routes in `routes/web.php`, API under `routes/api.php` with prefix `api/v1`.

## Domain rules that matter

- Ticket quota is `sold` on `ticket_categories`. Reserved on order creation, released on cancel or expiry. DB check constraint: `0 <= sold <= quota`.
- Order status: `PENDING` → `PAID` | `CANCELLED` (manual) | `EXPIRED` (scheduler).
- Gate validation requires an event. A ticket from another event returns `wrong_event` and stays unused.
- Mail is queued (`QUEUE_CONNECTION=database`). Run `php artisan queue:work` or mail never leaves.

## DB snapshots

`database/snapshots/` holds `pg_dump` output. Not tracked by git (contains password hashes).

Refresh:
```bash
docker exec concert-district-postgres-1 pg_dump -U concert_district -d concert_district --no-owner --no-privileges --inserts --column-inserts > database/snapshots/concert_district_data.sql
```

DBeaver: host `127.0.0.1`, port `5432`, db/user `concert_district`, password `secret`.

## Conventions

- Follow `CLAUDE.md` for Laravel conventions (Pint, `php artisan make:`, typed returns, PHPDoc shapes).
- Run `vendor/bin/pint --dirty --format agent` after PHP edits.

# Kutoarjo Social Club Management System

Laravel 13 + Inertia React application for Kutoarjo Social Club's public website, billiard reservations, manual payments, staff operations, and CMS.

## Requirements

- PHP 8.3+
- Composer
- Node.js compatible with Vite 8
- MySQL 8 for production; SQLite supported for local tests
- Nginx/CloudPanel, PHP-FPM, Supervisor, cron for production

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
```

Run locally:

```bash
composer run dev
```

## Database

Set MySQL values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kutoarjo_social_club
DB_USERNAME=ksc
DB_PASSWORD=
```

Primary domain IDs use ULIDs. Prices are stored as integer rupiah values.

## Authentication

Public registration is disabled. Development seeder credentials:

- `admin@kutoarjosocialclub.com` / `password123`
- `staff@kutoarjosocialclub.com` / `password123`

Production seeding refuses to run unless `SEED_ADMIN_PASSWORD` and `SEED_STAFF_PASSWORD` are set. Set `SEED_ADMIN_EMAIL` / `SEED_STAFF_EMAIL` too if the default emails are not desired.

## Quality checks

```bash
vendor/bin/pint
php artisan test
npx tsc --noEmit
npm run build
```

## Scheduler and queue

```bash
php artisan schedule:work
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

The scheduler expires unpaid bookings every minute. Production must run `schedule:run` through cron and `queue:work` through Supervisor.

## Storage

Gallery files use Laravel's public filesystem disk. Run:

```bash
php artisan storage:link
```

## Documentation

- [Architecture](docs/architecture.md)
- [Database](docs/database.md)
- [Booking Rules](docs/booking-rules.md)
- [CloudPanel Deployment](docs/deployment-cloudpanel.md)
- [Testing](docs/testing.md)
- [Assumptions](docs/assumptions.md)

# CloudPanel Deployment

## Stack

CloudPanel, Nginx, PHP-FPM 8.3+, MySQL 8, Supervisor, cron. Document root must target `/public`.

## Deploy

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=mysql
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SEED_ADMIN_PASSWORD=<strong unique password>
SEED_STAFF_PASSWORD=<strong unique password>
```

Never deploy `.env` from source control. Do not run production seeders without real staff credentials and real payment settings.

## Scheduler

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Supervisor

```ini
[program:ksc-worker]
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3 --timeout=90
directory=/path/to/project
autostart=true
autorestart=true
user=cloudpanel
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/worker.log
stopwaitsecs=3600
```

Run `sudo supervisorctl reread`, `sudo supervisorctl update`, then `sudo supervisorctl start ksc-worker:*`.

## Operations

Back up MySQL and `storage/app/public`. Monitor application and worker logs. Test `/up`, login, booking creation, payment confirmation, scheduler expiration, and status lookup after deploy.

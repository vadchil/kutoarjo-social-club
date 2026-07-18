# Testing

```bash
php artisan migrate:fresh --seed
vendor/bin/pint
php artisan test
npx tsc --noEmit
npm run build
php artisan route:list
php artisan schedule:list
```

Tests cover authentication, ULIDs/relationships, pricing boundaries, availability conflicts, booking creation, public booking endpoints, expiration command, and scheduler registration.

MySQL-specific concurrency behavior should also be exercised against a MySQL 8 test database before production deployment.

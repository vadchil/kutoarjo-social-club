# Verify

Runtime verification recipe for this Laravel/Inertia app.

1. Clear caches: `php artisan optimize:clear`.
2. Use an isolated sqlite DB for safe driving:
   ```bash
   rm -f storage/framework/verify.sqlite
   touch storage/framework/verify.sqlite
   DB_CONNECTION=sqlite DB_DATABASE="$(pwd)/storage/framework/verify.sqlite" php artisan migrate:fresh --seed --force
   ```
3. Start app:
   ```bash
   DB_CONNECTION=sqlite DB_DATABASE="$(pwd)/storage/framework/verify.sqlite" php artisan serve --host=127.0.0.1 --port=8017
   ```
4. Drive public booking through HTTP: GET `/booking/billiard`, parse CSRF/table/payment IDs from Inertia payload, POST `/booking/billiard`, confirm redirect to `/booking/billiard/success/{code}`.
5. Drive auth/authorization: login at `/admin/login`; staff should reach `/admin` and `/admin/bookings`, but receive 403 for `/admin/settings`, `/admin/gallery`, `/admin/faqs`.
6. Probe invalid public booking: public POST should only accept `booking_type=online` and require `payment_method_id`.

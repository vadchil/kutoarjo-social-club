# Database

Production target: MySQL 8, InnoDB, utf8mb4.

Domain tables: `users`, `billiard_tables`, `bookings`, `booking_histories`, `booking_payments`, `payment_methods`, `pricing_rules`, `galleries`, `faqs`, `site_settings`, plus framework cache/queue/session tables.

ULIDs identify domain records. Foreign keys preserve relationships. Booking indexes support table/status/time overlap checks and code/phone lookup. Monetary values are integer rupiah. Historical bookings store price snapshots.

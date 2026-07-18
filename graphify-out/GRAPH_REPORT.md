# Graph Report - .  (2026-07-19)

## Corpus Check
- Corpus is ~24,023 words - fits in a single context window. You may not need a graph.

## Summary
- 509 nodes · 831 edges · 55 communities (45 shown, 10 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 60 edges (avg confidence: 0.82)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Booking Domain Models
- HTTP Controllers
- Composer Metadata
- Booking Test Flow
- User Factories Auth
- Frontend Dependencies
- Public Pages UI
- Composer Scripts
- Admin UI Pages
- Booking Architecture Docs
- TypeScript Config
- Request Validation
- Inertia Middleware
- Availability Tests
- Pricing Rules Tests
- Service Providers
- Booking Form UI
- Booking Success UI
- Example Unit Tests
- Booking Status UI
- Deferred Scope
- Deployment Workers
- Booking Lookup UI
- Inertia Types
- Robots Policy
- CSS Types
- Unpaid Hold Policy

## God Nodes (most connected - your core abstractions)
1. `Booking` - 38 edges
2. `BilliardTable` - 21 edges
3. `SiteSetting` - 21 edges
4. `TestCase` - 18 edges
5. `PaymentMethod` - 17 edges
6. `User` - 17 edges
7. `PricingRule` - 16 edges
8. `ValidateBookingAvailabilityTest` - 16 edges
9. `Faq` - 15 edges
10. `Gallery` - 15 edges

## Surprising Connections (you probably didn't know these)
- `Historical Price Snapshots` --conceptually_related_to--> `Dynamic Pricing Rule Search`  [INFERRED]
  docs/database.md → CUsersSATSUZY.claudeplanscrystalline-roaming-spring-agent-a5e495afa3bfac534.md
- `Test Coverage` --conceptually_related_to--> `Dynamic Pricing Rule Search`  [INFERRED]
  docs/testing.md → CUsersSATSUZY.claudeplanscrystalline-roaming-spring-agent-a5e495afa3bfac534.md
- `ValidateBookingAvailabilityTest` --references--> `ValidateBookingAvailability`  [EXTRACTED]
  tests/Unit/ValidateBookingAvailabilityTest.php → app/Actions/Booking/ValidateBookingAvailability.php
- `CalculateBookingPriceTest` --references--> `CalculateBookingPrice`  [EXTRACTED]
  tests/Unit/CalculateBookingPriceTest.php → app/Actions/CalculateBookingPrice.php
- `CreateBookingTest` --references--> `BilliardTable`  [EXTRACTED]
  tests/Feature/Booking/CreateBookingTest.php → app/Models/BilliardTable.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Booking Creation Integrity** — docs_architecture_booking_transactions, docs_architecture_billiard_table_locking, docs_booking_rules_conflict_formula, docs_database_booking_indexes, docs_testing_mysql_concurrency_testing [INFERRED 0.95]
- **Dynamic Pricing Model** — docs_database_pricing_rules_table, docs_database_integer_rupiah, docs_database_price_snapshots, docs_booking_rules_weekday_billiard_price, docs_booking_rules_weekend_billiard_price, c_userssatsuzy_claudeplanscrystalline_roaming_spring_agent_a5e495afa3bfac534_dynamic_pricing_rule_search [INFERRED 0.95]
- **Production Operations Flow** — readme_scheduler_queue, docs_deployment_cloudpanel_scheduler_cron, docs_deployment_cloudpanel_supervisor_queue_worker, docs_deployment_cloudpanel_env_secret_handling [INFERRED 0.85]

## Communities (55 total, 10 thin omitted)

### Community 0 - "Booking Domain Models"
Cohesion: 0.09
Nodes (15): ExpireBookings, BilliardTable, Booking, BookingHistory, BookingPayment, Faq, PaymentMethod, SiteSetting (+7 more)

### Community 1 - "HTTP Controllers"
Cohesion: 0.12
Nodes (10): AdminBookingController, AdminCmsController, LoginController, Controller, PublicBookingController, PublicPagesController, Gallery, Illuminate\Http\RedirectResponse (+2 more)

### Community 2 - "Composer Metadata"
Cohesion: 0.05
Nodes (41): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+33 more)

### Community 3 - "Booking Test Flow"
Cohesion: 0.11
Nodes (13): CreateBooking, ValidateBookingAvailability, CalculateBookingPrice, DatabaseSeeder, Illuminate\Database\Seeder, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\JsonResponse (+5 more)

### Community 4 - "User Factories Auth"
Cohesion: 0.08
Nodes (14): User, BilliardTableFactory, BookingFactory, FaqFactory, GalleryFactory, PaymentMethodFactory, PricingRuleFactory, SiteSettingFactory (+6 more)

### Community 5 - "Frontend Dependencies"
Cohesion: 0.06
Nodes (32): concurrently, @inertiajs/react, laravel-vite-plugin, dependencies, @inertiajs/react, react, react-dom, devDependencies (+24 more)

### Community 6 - "Public Pages UI"
Cohesion: 0.09
Nodes (10): formatCurrency(), statusLabels, links, Props, Billiard(), fmt(), F, Item (+2 more)

### Community 7 - "Composer Scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 8 - "Admin UI Pages"
Cohesion: 0.08
Nodes (11): navigation, BilliardTable, Booking, BookingsIndexProps, PaymentMethod, F, Item, S (+3 more)

### Community 9 - "Booking Architecture Docs"
Cohesion: 0.09
Nodes (23): CalculateBookingPrice Plan, Dynamic Pricing Rule Search, Minute By Minute Pricing, Actions Business Logic, Billiard Table Locking, Booking Transactions, Inertia Bridge, Modular Laravel Monolith (+15 more)

### Community 10 - "TypeScript Config"
Cohesion: 0.12
Nodes (15): resources/js/**/*.d.ts, resources/js/**/*.ts, resources/js/**/*.tsx, compilerOptions, allowSyntheticDefaultImports, esModuleInterop, forceConsistentCasingInFileNames, jsx (+7 more)

### Community 11 - "Request Validation"
Cohesion: 0.20
Nodes (3): LoginRequest, StoreBookingRequest, Illuminate\Foundation\Http\FormRequest

### Community 12 - "Inertia Middleware"
Cohesion: 0.21
Nodes (6): HandleInertiaRequests, RequireRole, Closure, Illuminate\Foundation\Configuration\Middleware, Inertia\Middleware, Symfony\Component\HttpFoundation\Response

### Community 16 - "Booking Form UI"
Cohesion: 0.40
Nodes (5): BookingForm(), BookingProps, numberFormat(), PaymentMethod, Table

### Community 17 - "Booking Success UI"
Cohesion: 0.50
Nodes (4): BookingSuccess(), BookingSuccessProps, numberFormat(), PaymentMethod

### Community 32 - "Deferred Scope"
Cohesion: 0.67
Nodes (3): Excluded Features, External Padel Booking, Environment Secret Handling

### Community 33 - "Deployment Workers"
Cohesion: 0.67
Nodes (3): Scheduler Cron, Supervisor Queue Worker, Scheduler And Queue

## Knowledge Gaps
- **119 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+114 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **10 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Booking` connect `Booking Domain Models` to `HTTP Controllers`, `Booking Test Flow`, `User Factories Auth`, `Availability Tests`?**
  _High betweenness centrality (0.036) - this node is a cross-community bridge._
- **Why does `User` connect `User Factories Auth` to `Booking Domain Models`, `Booking Test Flow`?**
  _High betweenness centrality (0.015) - this node is a cross-community bridge._
- **Why does `ValidateBookingAvailabilityTest` connect `Availability Tests` to `Booking Domain Models`, `Booking Test Flow`?**
  _High betweenness centrality (0.014) - this node is a cross-community bridge._
- **Are the 7 inferred relationships involving `BilliardTable` (e.g. with `.execute()` and `.schedule()`) actually correct?**
  _`BilliardTable` has 7 INFERRED edges - model-reasoned connections that need verification._
- **Are the 16 inferred relationships involving `SiteSetting` (e.g. with `.execute()` and `.execute()`) actually correct?**
  _`SiteSetting` has 16 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _119 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Booking Domain Models` be split into smaller, more focused modules?**
  _Cohesion score 0.08944793850454227 - nodes in this community are weakly interconnected._
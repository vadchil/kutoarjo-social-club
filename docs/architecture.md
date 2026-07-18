# Architecture

Modular Laravel monolith. Inertia bridges Laravel controllers to React pages without a separate API.

- Controllers: HTTP orchestration only.
- Form Requests: boundary validation.
- Actions: pricing, availability, transactional booking creation.
- Models: relationships, casts, persistence.
- Policies/middleware: backend authorization.
- React pages: presentation and interactions.
- Database queue/session/cache: MVP infrastructure without Redis.

Critical booking writes use transactions. Availability locks the selected `billiard_tables` row before conflict queries.

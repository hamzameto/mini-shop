# ROADMAP: Mini-Shop

Complete the phases in order. Each phase lists the concepts it teaches and the steps to build.

## Status

- **Last update:** 2026-10-10
- **Done:** Phase 0 (committed and pushed to `main`)
- **In progress:** Phase 1 (enums, migrations, models and scopes done)
- **Next:** Phase 1, factories with states (`outOfStock()`, `admin()`)
- **Checks at last commit:** 62 Pest tests passing, Pint clean, Larastan level 7 with 0 errors

| Phase | Topic | Concepts |
|---|---|---|
| 0 | Setup and tour | Docker Compose, project structure, request lifecycle, Inertia, Pint, Larastan |
| 1 | Data model | Migrations, Eloquent, relations, enums, factories, seeders |
| 2 | Product catalogue | Routing, controllers, Inertia pages, Vue basics, pagination, filters |
| 3 | Admin area | Roles, middleware, policies, Form Requests, file upload, Vue forms |
| 4 | Cart and checkout | Sessions, Actions, transactions, row locking, events, queued mail |
| 5 | Order management | Enum logic, state transitions, TDD |
| 6 | REST API | Sanctum, API Resources, versioning, rate limiting |
| 7 | Quality | Stricter static analysis, Vitest, coverage, refactoring |
| 8 | CI and deployment prep | GitHub Actions, production config, README |

---

## Phase 0: Setup and tour

- [x] Check current stable versions (PHP, Laravel, Vue, Inertia, Pest)
- [x] Check PHP version and the `pdo_pgsql` / `pgsql` extensions, Composer and Node.js
- [x] Create the project with the Laravel Vue starter kit and Pest
- [x] `docker-compose.yml` with PostgreSQL (development and test databases) and Mailpit, app running with `php artisan serve` at `http://localhost:8000`
- [x] `phpunit.xml` pointed at the PostgreSQL test database
- [x] Pint and Larastan installed, with `composer pint` and `composer stan` scripts, so every commit can be checked from the start
- [x] `.gitignore` adjusted (`.env` ignored), `.env` filled for local development, `.env.example` with placeholders only
- [x] One-time git setup commands for the developer (he creates the GitHub repo, then runs the commands and pushes)
- [x] Explain each top-level folder and the starter-kit files (auth pages, layouts, `HandleInertiaRequests`)
- [x] Explain the request lifecycle: browser → `public/index.php` → routing → middleware → controller → Inertia → Vue page. Write it down in `docs/request-lifecycle.md`
- [x] Run and explain the starter-kit tests

Notes from Phase 0:
- Installed versions: PHP 8.5, Laravel 13, Inertia 3, Vue 3.5, Vite 8, Tailwind 4, Pest 5, Pint 1.32, Larastan 3, PostgreSQL 18.
- The Docker database uses host port 5433, because a Windows PostgreSQL service already uses 5432.
- The starter kit already has a `users` table with two-factor columns, a `passkeys` table, and Fortify authentication. Phase 1 adds the `role` column to `users` with a new migration.
- The starter kit's tests were converted from PHPUnit to Pest.

## Phase 1: Data model

Tables:
- `users`: + `role` (`customer`, `admin`)
- `categories`: `name`, `slug`
- `products`: `category_id`, `name`, `slug`, `description`, `price_cents`, `stock`, `image_path`, `is_active`
- `orders`: `user_id` (nullable), `email`, `name`, `street`, `zip`, `city`, `status`, `total_cents`, `placed_at`
- `order_items`: `order_id`, `product_id` (nullable), `product_name`, `unit_price_cents`, `quantity`

Steps:
- [x] Enums `UserRole`, `OrderStatus` (`pending`, `paid`, `shipped`, `cancelled`)
- [x] Migrations with foreign keys, indexes and `onDelete` rules
- [x] Models with casts and relations
- [x] Scopes `Product::active()`, `Product::inStock()`
- [ ] Factories with states (`outOfStock()`, `admin()`)
- [ ] Seeder: 1 admin, 5 customers, 6 categories, ~40 products, ~15 orders
- [ ] Tests for relations, scopes and casts
- [ ] Explain eager vs. lazy loading and the N+1 problem with a Tinker example

## Phase 2: Product catalogue

- [ ] `ProductController@index` / `@show` with slug route binding
- [ ] `Products/Index.vue`: grid and pagination
- [ ] Filters in the URL: category, search, in stock only, sort by price/name
- [ ] `Products/Show.vue` with an add-to-cart button (works in Phase 4)
- [ ] Components `ProductCard`, `Pagination`, `PriceTag`, the `formatPrice()` helper, TypeScript types
- [ ] Check for N+1 queries with the query log
- [ ] Tests: only active products, filters, 404 for unknown or inactive products

## Phase 3: Admin area

- [ ] Admin middleware/gate and an `/admin` route group
- [ ] `ProductPolicy`, `CategoryPolicy`
- [ ] Category CRUD
- [ ] Product CRUD with `StoreProductRequest` / `UpdateProductRequest`
- [ ] Image upload (public disk), delete the old image on update
- [ ] Admin Vue pages with `useForm`, field errors and flash messages
- [ ] Dashboard: order count, revenue, low-stock products
- [ ] Tests: admin CRUD, 403 for customers and guests, validation, upload with `Storage::fake()`

## Phase 4: Cart and checkout

- [ ] `CartService` (session): add, update, remove, total. Tests first
- [ ] Cart page and a header counter (shared Inertia props)
- [ ] Checkout form, prefilled for logged-in users
- [ ] `PlaceOrderAction`: one transaction with `lockForUpdate`, stock check, order and items, stock decrement, clear cart
- [ ] `OrderPlaced` event → queued listener → confirmation email (Mailpit)
- [ ] Order confirmation page and a "My orders" page
- [ ] Tests: successful order, stock reduced, out-of-stock rollback, mail queued, customers can't see other customers' orders

## Phase 5: Order management

- [ ] `OrderStatus::canTransitionTo()`: `pending → paid → shipped`, `pending|paid → cancelled`. Tests first
- [ ] `ChangeOrderStatusAction`: cancelling restores stock (transaction)
- [ ] Admin order list (filter by status, search by email) and detail page with only the allowed status buttons
- [ ] Email to the customer when an order is shipped
- [ ] Tests for transitions and stock restore

## Phase 6: REST API

- [ ] Sanctum tokens, created on the admin profile page
- [ ] `GET /api/v1/products`, `GET /api/v1/products/{slug}` (public)
- [ ] `GET /api/v1/orders`, `GET /api/v1/orders/{id}`, `PATCH /api/v1/orders/{id}/status` (admin token)
- [ ] `ProductResource`, `OrderResource`, consistent JSON errors
- [ ] Rate limiting
- [ ] Tests: status codes, JSON structure, 401/403/422
- [ ] `docs/API.md` with curl examples

## Phase 7: Quality

- [ ] Raise the Larastan level and fix the new findings
- [ ] Vitest tests for `PriceTag`, `ProductCard` and the cart counter
- [ ] Coverage report
- [ ] Refactoring pass with short explanations

## Phase 8: CI and deployment prep

Claude Code writes the files below. The developer sets up Neon and Render, adds the environment variables and deploys himself. Everything must work on the **free tiers** of Render and Neon.

- [ ] GitHub Actions workflow: Pint, Larastan, Pest (PostgreSQL service), frontend build, Vitest. Replace the starter kit's `.github/workflows/tests.yml`, which is set to manual trigger only since Phase 0, and turn the push / pull request triggers back on
- [ ] `Dockerfile` for Render's free web service (PHP, built frontend assets, migrations on start)
- [ ] Free-tier production settings: `QUEUE_CONNECTION=sync` (no paid worker), `MAIL_MAILER=log` (no paid mail service), PostgreSQL SSL settings for Neon
- [ ] Trusted proxies configured, so HTTPS URLs and assets work behind Render's proxy
- [ ] Demo product images committed in the repo, so they survive restarts (free hosting doesn't keep uploaded files)
- [ ] `.env.example` with all variables, and a demo seeder with a demo admin login
- [ ] `docs/DEPLOYMENT.md`: step-by-step checklist for the developer (create the Neon database, create the Render web service from the GitHub repo, which environment variables to set, first deploy)
- [ ] `README.md`: description, features, stack, key decisions (cents, snapshots, actions, transactions), local setup, tests, placeholders for screenshots and the live-demo link

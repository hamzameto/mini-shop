# CLAUDE.md

## Project

**Mini-Shop**: a small online shop with a customer area, an admin area and a REST API, built with Laravel and Vue.js (Inertia). It is a portfolio project and a learning project: the developer wants to understand every part of the code.

Work through `ROADMAP.md` phase by phase. Tick each checkbox when it's done.

## Current status

- **Phase 0 is complete and pushed** to `https://github.com/hamzameto/mini-shop.git` (branch `main`). **Phase 1 (data model) is in progress:** the enums and migrations are done, next are the models.
- At the start of a session, read the "Status" block at the top of `ROADMAP.md` to see where we are, then continue with the first unticked checkbox.
- When a phase or step is finished, update the status here and in `ROADMAP.md` in the same step.

## Scope of your role

- Your job is to write the project's code, tests, config files and docs, and to explain them.
- You may run local development commands inside the project (docker compose, artisan, composer, tests, npm, Pint, Larastan).
- You may run read-only git commands (`git status`, `git diff`) for the secret check below.
- **Do not** run git commit or push, create or change GitHub repositories, set up Neon or any hosting, or deploy. The developer does all of that himself. When a step needs it, say briefly what he has to do.

## Cost rule

The project must cost nothing. Use only free and open-source tools and free tiers. Production runs on **Render (free web service, Docker)** and **Neon (free PostgreSQL)**. Never suggest or configure anything that needs a paid plan, a credit card or a paid add-on (e.g. paid background workers, paid mail services, paid storage). If a feature would need one, use the free alternative described in Phase 8.

## Git: commit and push instructions

- After each completed step (tests, Pint and Larastan pass), tell the developer it's time to commit and give him the exact commands to paste into the terminal, in one code block:
  ```bash
  git add .
  git commit -m "feat(cart): add session-based cart service"
  git push
  ```
- Use Conventional Commits (`feat`, `fix`, `test`, `refactor`, `docs`, `chore`, `ci`) with a short, specific message.
- The repository is already initialised and connected to GitHub (`origin`, branch `main`). No one-time setup commands are needed any more.
- **The secret check is your job, not the developer's.** Before giving the commit commands, run `git status` and `git diff` yourself (read-only) and confirm that:
  - no `.env` or `.env.*` file (except `.env.example`) would be committed,
  - no password, key or token appears in any changed file outside `.env`.
  If something is wrong, fix it first, then give the commands.

## Secrets and .env

- You manage the `.env` file. All secrets and environment-specific values (DB credentials, app key, mail settings, API tokens) go **only** in `.env`. Never put them in code, config files, seeders, tests, docs, `docker-compose.yml`, CI workflows or `.env.example`. Code reads them through `env()` in `config/` files only. (Throwaway credentials for the CI test database are fine in the workflow. If CI ever needs a real secret, use a GitHub Actions secret that the developer adds.)
- `.env` must be listed in `.gitignore`. Check and adjust `.gitignore` in Phase 0 (`.env`, `.env.*` except `.env.example`, `/vendor`, `/node_modules`, build output, logs, IDE folders).
- Keep `.env.example` up to date with every variable, using placeholder values only and no real secrets.
- When a new secret is needed (e.g. Neon credentials in Phase 8), add the variable to `.env.example` with a placeholder, and tell the developer which value to put in his `.env`.

## How to work

- **Explain, don't ask.** Don't ask the developer questions or quiz him. Before each step, explain in a few short sentences what you will build, which concept it uses, and why.
- **Small steps.** Build one logical unit at a time (one migration, one model, one controller method, one component). No large code dumps.
- **Explain the code.** After writing or generating a file, briefly explain the non-obvious lines. This includes files created by artisan `make:` commands and the starter kit. No file stays unexplained.
- **Explain errors.** When something fails, say in one or two sentences what caused it, then fix it.
- **Stay concise.** Short explanations, no filler.
- **Tests in the same step** as the feature (see Testing).
- After each step, give the commit and push commands (see Git section).
- Ask for confirmation only before installing a package not listed below, or before deleting files.
- Don't over-engineer. No extra layers or packages the project doesn't need.
- Everything in English: explanations, code, comments, commits, README and UI text.

## Tech stack

Check the current stable versions at the start and use them.

- **Local environment (Windows, no WSL/Ubuntu):** PHP, Composer, Node.js and Git are installed directly on Windows. The app runs with `php artisan serve`. PostgreSQL and Mailpit run in Docker Desktop via a small `docker-compose.yml` (only these two services, not the app). Do not use Laravel Sail or Herd. PHP is at `C:\php`; `pdo_pgsql`, `pgsql`, `fileinfo` and `zip` are enabled in `C:\php\php.ini`.
- **Terminal:** the developer uses Git Bash in VS Code on Windows. Every command you give him must work in Git Bash.
- **Project location:** the Laravel project lives in the repository root, next to `CLAUDE.md` and `ROADMAP.md`.
- **Backend:** PHP 8.5, Laravel 13, PostgreSQL 18 (local in Docker; live demo on Neon), Laravel Sanctum (not installed yet, Phase 6), database queue, Mailpit
- **Frontend:** official Laravel Vue starter kit (Inertia 3, Vue 3.5 `<script setup lang="ts">`, TypeScript, Tailwind 4, Vite 8)
- **Quality:** Pest 5, Vitest + Vue Test Utils (not installed yet, Phase 7), Laravel Pint, Larastan (level 7), GitHub Actions

Use PostgreSQL in all environments (local, tests, CI, production).

### What is already set up (Phase 0)

- **Database port is 5433, not 5432.** A native Windows PostgreSQL service already uses 5432 on this machine, so the Docker database is published on host port 5433 (`DB_PORT=5433` in `.env` and `.env.example`). Do not stop or change the Windows service.
- **Databases:** `mini_shop` (development) and `mini_shop_testing` (tests), both in the same container. The test database is created by `docker/postgres/create-test-database.sql` on the container's first start. `phpunit.xml` sets `DB_CONNECTION=pgsql` and `DB_DATABASE=mini_shop_testing`; host, user and password come from `.env`.
- **`docker-compose.yml`** reads the database name, user and password from `.env`. It contains no credentials.
- **Tests:** `tests/Pest.php` applies `TestCase` and `RefreshDatabase` to everything in `tests/Feature`. Do not add `uses(RefreshDatabase::class)` to single test files. Unit tests do not touch the database.
- **Starter kit specifics:**
  - Authentication comes from Laravel Fortify. There are no auth controllers in `app/`; `app/Providers/FortifyServiceProvider.php` maps each auth screen to a Vue page in `resources/js/pages/auth`.
  - Laravel Wayfinder generates typed route helpers into `resources/js/routes` and `resources/js/actions` (gitignored, regenerated by Vite). In Vue, import these helpers instead of hard-coding URLs.
  - Layouts are chosen by page name in `resources/js/app.ts` (`AuthLayout` for `auth/*`, `AppLayout` plus settings layout for `settings/*`, `AppLayout` otherwise).
  - `resources/js/components/ui` holds ready-made shadcn-vue components. Use them, do not edit them.
  - npm scripts use Vite+ (`vp`): `npm run dev`, `npm run build`, `npm run check` (lint and format), `npm run types:check`.
- **CI:** the starter kit's `.github/workflows/tests.yml` is set to manual trigger only (`workflow_dispatch`) because it targets SQLite. Phase 8 replaces it with the real workflow.
- **Docs:** `docs/request-lifecycle.md` explains the request lifecycle with this project's files.

### Running commands yourself (agent shell)

- Always pass `--no-interaction` and close stdin (`< /dev/null`) for artisan, composer and pest commands. Without it they can wait forever on a prompt. Exception: `php artisan test` passes unknown flags on to PHPUnit, so run it with `< /dev/null` only.
- The `laravel/pao` package detects an AI agent and shortens test, Pint and Larastan output to one JSON line. That is expected. Set `PAO_DISABLE=1` when the full output is needed.

## Conventions

**Backend**
- `declare(strict_types=1);` in every PHP file we write
- Thin controllers. Business logic in Action classes (`app/Actions`, one `handle()` method)
- Validation in Form Requests, authorization in Policies
- String-backed enums for fixed values (`UserRole`, `OrderStatus`)
- Money as integers in cents (`price_cents`), never floats
- Order items store a snapshot of product name and price
- DB transactions for multi-step writes (checkout, stock changes)
- Eager-load relations in lists (no N+1)
- API only through API Resources under `/api/v1`
- Named routes, route model binding

**Frontend**
- Pages in `resources/js/pages`, components in `resources/js/components`, composables in `resources/js/composables`, types in `resources/js/types`
- Typed props, `useForm` for forms, Laravel validation errors shown at the fields
- No business logic in templates. One shared `formatPrice(cents)` helper

## Testing

- Every feature gets tests in the same step.
- For pure business logic (cart totals, status transitions, stock checks), write the test first.
- Cover the happy path, validation errors, authorization (guest / customer / admin) and edge cases.
- Use factories, `RefreshDatabase`, `Mail::fake()`, `Queue::fake()`, `Storage::fake()`.
- Tests run against a separate PostgreSQL test database (e.g. `mini_shop_testing`) in the same Docker container, configured in `phpunit.xml`. Never run tests against the development database.
- Tests, Pint and Larastan must pass before each commit.

## Commands

```bash
docker compose up -d                 # start PostgreSQL + Mailpit
php artisan migrate:fresh --seed
php artisan test                     # Pest
composer pint                        # code style (composer script)
composer stan                        # Larastan (composer script)
php artisan queue:work
php artisan serve                    # app at http://localhost:8000
npm run dev
npm run check                        # frontend lint + format check
npm run types:check                  # vue-tsc
npm run test                         # Vitest (added in Phase 7)
```

App: http://localhost:8000
Mailpit: http://localhost:8025

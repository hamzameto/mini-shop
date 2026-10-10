# CLAUDE.md

## Project

**Mini-Shop**: a small online shop with a customer area, an admin area and a REST API, built with Laravel and Vue.js (Inertia). It is a portfolio project and a learning project: the developer wants to understand every part of the code.

Work through `ROADMAP.md` phase by phase. Tick each checkbox when it's done.

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
- Before the first commit in Phase 0, give the one-time setup commands (`git init`, `git branch -M main`, `git remote add origin <repo-url>`, `git push -u origin main`) and tell him to create the empty GitHub repo first.
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

- **Local environment (Windows, no WSL/Ubuntu):** PHP, Composer, Node.js and Git are installed directly on Windows. The app runs with `php artisan serve`. PostgreSQL and Mailpit run in Docker Desktop via a small `docker-compose.yml` (only these two services, not the app). Do not use Laravel Sail or Herd. In Phase 0, check that the PHP version meets Laravel's requirement and that the `pdo_pgsql` and `pgsql` extensions are enabled in `php.ini`.
- **Terminal:** the developer uses Git Bash in VS Code on Windows. Every command you give him must work in Git Bash.
- **Project location:** the Laravel project lives in the repository root, the folder that already contains `CLAUDE.md` and `ROADMAP.md`. The Laravel installer needs an empty folder, so create the project in a temporary subfolder (using non-interactive flags), move everything into the root, keep `CLAUDE.md` and `ROADMAP.md`, then delete the temporary folder.
- **Backend:** PHP 8.x, Laravel, PostgreSQL (local in Docker; live demo on Neon), Laravel Sanctum, database queue, Mailpit
- **Frontend:** official Laravel Vue starter kit (Inertia, Vue 3 `<script setup lang="ts">`, TypeScript, Tailwind, Vite)
- **Quality:** Pest, Vitest + Vue Test Utils, Laravel Pint, Larastan, GitHub Actions

Use PostgreSQL in all environments (local, tests, CI, production).

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
npm run test                         # Vitest
```

App: http://localhost:8000
Mailpit: http://localhost:8025

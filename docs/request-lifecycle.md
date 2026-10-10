# Request lifecycle

What happens when a logged-in user opens `http://localhost:8000/dashboard`.

```
browser → public/index.php → bootstrap/app.php → routing → middleware → controller → Inertia → Vue page
```

## 1. Browser

The browser sends `GET /dashboard`. On the first visit this is a normal full-page request. Later clicks on Inertia links send an XHR request with the header `X-Inertia: true` instead.

## 2. `public/index.php`

The only PHP file the web server exposes; every request enters here. It loads Composer's autoloader, gets the application object from `bootstrap/app.php`, and calls `$app->handleRequest(Request::capture())`. `Request::capture()` wraps the raw PHP input (`$_GET`, `$_POST`, cookies, headers) in one `Request` object.

## 3. `bootstrap/app.php`

Configures the application: which route files to load (`withRouting`), which middleware to add (`withMiddleware`), and how exceptions are rendered (`withExceptions`). The service providers listed in `bootstrap/providers.php` boot here too, for example `FortifyServiceProvider`, which registers the login and registration pages.

## 4. Routing

The router matches method and URL against `routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
```

The match gives the router three things: the middleware to run (`auth`, `verified`), the action, and the route name (`dashboard`).

## 5. Middleware

Middleware are layers around the action. Each one receives the request, may stop it, and otherwise passes it on with `$next($request)`.

- The `web` group (every route in `web.php`): cookie encryption, session start, CSRF protection, plus the three appended in `bootstrap/app.php`: `HandleAppearance` (light/dark mode cookie), `HandleInertiaRequests` and `AddLinkHeadersForPreloadedAssets`.
- `auth`: a guest is redirected to `/login` here and never reaches the action.
- `verified`: a user without a verified email is redirected to the verification notice.

`HandleInertiaRequests::share()` defines the props every page receives: `name`, `auth.user` and `sidebarOpen`.

## 6. Controller

The action builds the response. `Route::inertia('dashboard', 'Dashboard')` is a shortcut for a controller that only does this:

```php
return Inertia::render('Dashboard');
```

A real controller (see `Settings/ProfileController`) reads the validated input, calls models or Action classes, and returns `Inertia::render('page/Name', [...props])` or a redirect.

## 7. Inertia

`Inertia::render()` produces a page object: component name, props (page props merged with the shared props), URL and asset version. How it is sent depends on the request:

- **First visit:** the Blade view `resources/views/app.blade.php` is returned as HTML. It contains the page object and the `@vite` tags that load `resources/js/app.ts`.
- **Later visits** (`X-Inertia` header): only the page object is returned, as JSON. No full reload.

## 8. Vue page

`resources/js/app.ts` calls `createInertiaApp()`. It resolves the component name `Dashboard` to `resources/js/pages/Dashboard.vue`, picks a layout by name (`AppLayout` here, `AuthLayout` for `auth/*`), and mounts the component with the props from the page object. On later visits Inertia swaps only the page component and keeps the layout.

## The way back

The response travels back through the same middleware in reverse order (the session is saved, cookies are encrypted) and `public/index.php` sends it to the browser.

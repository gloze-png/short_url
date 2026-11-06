# URL Shortener with Click Analytics

A full-stack Laravel application that converts long URLs into 6-character short links, redirects visitors, and records click analytics. Guests can shorten links from the home page; registered users manage their links and view analytics from a dashboard.

> Full technical design, schema decisions and security notes are in [DOCUMENTATION.md](DOCUMENTATION.md).

## Features

- Shorten any http/https URL into a 6-character, collision-free code
- Optional custom alias and optional expiration date
- One-click "Copy to Clipboard" on the result card
- Redirect endpoint (`/s/{code}`) that logs IP address, User-Agent and Referer
- Custom **410 Gone** page for expired or deactivated links
- User dashboard: total links, total clicks, active links, paginated table, toggle and delete
- Per-link analytics: clicks per day (Chart.js), top referrers, recent clicks log
- Rate limiting on link creation (10 per minute per IP)
- Feature tests for creation, redirection and expiry

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel (PHP) |
| Auth scaffolding | Laravel Breeze (Blade stack) |
| Frontend | Blade templates, Tailwind CSS, Vite |
| Charts | Chart.js |
| Database | MySQL (SQLite works too) |
| Testing | PHPUnit (Laravel Feature tests) |

## Requirements

- PHP 8.2 or higher (check your Laravel version's requirements)
- Composer
- Node.js and npm
- MySQL (or SQLite with the `pdo_sqlite` extension enabled)

## Installation

```bash
# 1. Clone and enter the project
git clone <your-repository-url> url-shortener
cd url-shortener

# 2. Install dependencies
composer install
npm install

# 3. Environment file and app key
cp .env.example .env
php artisan key:generate

# 4. Configure the database in .env (DB_CONNECTION, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Create the tables
php artisan migrate

# 6. Start the app (two terminals)
npm run dev
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Usage

1. As a guest: open the home page, paste a URL, optionally add an alias or expiry date, and click 

**Shorten URL**. Copy the result.

2. **As a user:** register or log in, then open `/dashboard` to see your links. Click a click-count badge to open that link's analytics.

3. **As a visitor:** open `/s/{code}` and you are redirected to the original URL. Expired or deactivated links show the 410 page.

## Routes

| Method | URI | Auth | Purpose |
|---|---|---|---|
| GET | `/` | No | Landing page and quick-shorten form |
| POST | `/urls` | No (throttled) | Create a short link |
| GET | `/s/{short_code}` | No | Redirect and log click |
| GET | `/dashboard` | Yes | Stats and links table |
| GET | `/urls/{shortUrl}/analytics` | Yes (owner) | Link analytics |
| PATCH | `/urls/{shortUrl}/toggle` | Yes (owner) | Activate or deactivate |
| DELETE | `/urls/{shortUrl}` | Yes (owner) | Delete link and its clicks |

Breeze adds the login, register, password reset and profile routes.

## Testing

```bash
php artisan test
```

The tests cover:

1. A guest can create a short link
2. A short link redirects and logs a click
3. An expired link returns **410** and logs no click

Optional extra tests cover the rate limit (429) and the ownership check (403).

> **Important:** the tests use `RefreshDatabase`, which wipes the connected database. In `phpunit.xml`, always point tests at a **separate** database (for example SQLite `:memory:`, or a dedicated MySQL database such as `url_shortener_test`). Never point them at your development database.

## Project Structure (key files)

```
app/
  Http/
    Controllers/
      ShortUrlController.php          # store, toggle, destroy
      RedirectUrlController.php       # invokable: redirect + click logging
      DashboardController.php         # invokable: stats + paginated links
      ShortUrlAnalyticsController.php # invokable: per-link analytics
    Requests/StoreShortUrlRequest.php # validation rules
  Models/
    ShortUrl.php                      # hasMany clicks, scopes, helpers
    UrlClick.php                      # belongsTo shortUrl
  Providers/AppServiceProvider.php    # named rate limiter
database/migrations/                  # short_urls, url_clicks
resources/views/
  home.blade.php                      # landing page
  dashboard.blade.php                 # user dashboard
  analytics.blade.php                 # analytics detail
  errors/410.blade.php                # expired / inactive link page
routes/web.php
tests/Feature/ShortUrlTest.php
```

## Troubleshooting

| Symptom | Likely cause and fix |
|---|---|
| 403 when submitting the form | `authorize()` in `StoreShortUrlRequest` returns `false`. Set it to `true`. |
| 419 Page Expired | The form is missing `@csrf`. |
| 500 on `/dashboard` mentioning `$stats` | The old Breeze closure route for `/dashboard` is still in `routes/web.php`. Remove it and check with `php artisan route:list --path=dashboard`. |
| Styles missing | `npm run dev` is not running. |
| Tests wipe my data | `phpunit.xml` points at the development database. Use a separate test database. |

## Known Limitations and Future Improvements

- Daily analytics are grouped by UTC date, so a late-night Lagos click may count toward the previous day.
- User-Agent strings are shown raw; a parsing library would give browser and device names.
- Click logging happens during the redirect; a queue would remove that delay at scale.
- Behind a proxy or load balancer, configure trusted proxies so `$request->ip()` is the visitor's real IP.
- Ownership checks are inline; Laravel Policies would scale better.

## License

For training and educational use.
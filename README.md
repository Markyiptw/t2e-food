# t2e-food

An OpenRice restaurant map and CSV exporter built with Laravel. It periodically
scrapes the OpenRice public API, stores restaurants (name, address, coordinates,
opening hours, categories, district, status) in Postgres, and exposes:

- **`/map`** — a Leaflet map with marker clustering. Markers are cursor-paginated
  (1000 per page) and can be filtered to restaurants open at a given time for a
  given duration.
- **`/export`** — a streaming CSV export (name, district, address, coordinates,
  opening hours, categories), filterable to restaurants open within a time window.

## Tech stack

- **Backend:** Laravel 13, PHP 8.5, Postgres 17
- **Frontend:** Vite 6, Tailwind CSS v4, Alpine.js 3, htmx, Leaflet + markercluster
- **Testing:** PHPUnit 12
- **Dev environment:** Laravel Sail (Docker)
- **Deployment:** Kamal + Docker (serversideup/php FrankenPHP image)

## Requirements

- PHP 8.5, Composer
- Node.js 24 + npm
- Docker (for Sail)

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# Bring up Sail (Postgres, the app, etc.)
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

Then visit `http://localhost`.

Alternatively, run the stack natively without Sail:

```bash
composer run dev
```

This starts `php artisan serve`, the queue worker, Pail logs, and Vite together.

## The scraper

`App\Jobs\ScrapeOpenriceRestaurants` is a unique queued job that paginates the
OpenRice API district by district and syncs each restaurant via
`App\Services\Openrice\SyncOpenriceRestaurant`. Pagination state is kept in the
cache under `scrape_openrice_restaurants.query_parameters`. Dispatch it through
your queue worker (`php artisan queue:work`).

> **Disclaimer:** This project queries OpenRice's public web API. It is provided
> for educational purposes only. Scraping may be subject to OpenRice's Terms of
> Service and applicable database-rights / copyright law in your jurisdiction.
> You are responsible for ensuring your use complies with those terms and the law.
> The authors of this project accept no liability for how it is used.

## Deployment

The app is deployed with [Kamal](https://kamal-deploy.org/) to a single host
running the web container, a queue worker, and the scheduler, with a Postgres
accessory and kamal-proxy (TLS) in front. See `config/deploy.yml`.

Secrets (`APP_KEY`, `DB_PASSWORD`, `POSTHOG_API_KEY`) are loaded from
`.kamal/secrets`, which reads from your environment — no raw credentials are
committed. Populate `.kamal/secrets` on your deploy machine before deploying.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

t2e-food is open-sourced software licensed under the [MIT license](LICENSE).

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
- **Dev environment:** Dev Container (VS Code) on Laravel Sail's `docker-compose.yml`
- **Deployment:** Kamal + Docker (serversideup/php FrankenPHP image)

## Requirements

- [Docker](https://www.docker.com/)
- [VS Code](https://code.visualstudio.com/) with the
  [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)
  extension

## Local setup

This project uses a Dev Container (built on Laravel Sail's `docker-compose.yml`)
rather than the Sail CLI.

Copy `.env.example` to `.env` **before** reopening in container —
`docker-compose.yml` reads variables like `DB_PASSWORD` at compose-up time, so
the Postgres service needs them present to initialize correctly:

```bash
cp .env.example .env
```

Then open the repository in VS Code and choose **Reopen in Container**; VS Code
builds the `laravel.test` container (with Postgres, Redis, Typesense, Mailpit,
and Selenium) and runs the post-create steps (`composer install`, `npm install`)
for you.

Once inside the container, generate the app key, run migrations, and start Vite:

```bash
php artisan key:generate
php artisan migrate
npm run dev
```

Then visit `http://localhost`.

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

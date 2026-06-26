# Contributing

Thanks for your interest in contributing to t2e-food! This document covers the
basics of getting set up and the conventions to follow.

## Local setup

See the [README](README.md) for local setup instructions. After your environment
is running:

```bash
./vendor/bin/sail artisan migrate
```

## Running the tests

```bash
php artisan test --compact
```

Run a single file or test:

```bash
php artisan test --compact tests/Feature/ScrapeOpenriceRestaurantsJobTest.php
php artisan test --compact --filter=testName
```

## Code style

- **PHP:** formatted with [Laravel Pint](https://github.com/laravel/pint). Run
  `vendor/bin/pint` before committing. CI expects Pint-clean code.
- **Blade / frontend:** formatted with Prettier (using
  `prettier-plugin-blade` and `prettier-plugin-tailwindcss`).

## Pull requests

1. Fork the repository and create a branch from `main`.
2. Make your change with tests covering happy paths, failure paths, and edge
   cases. Don't remove existing tests.
3. Ensure tests pass and code is formatted (see above).
4. Open a pull request with a clear description of what and why.

Keep changes focused. If your change is large, consider opening an issue to
discuss it first.

# TFLI-App — URL Shortener

A lightweight PHP URL shortener. Submit a long URL (with an optional expiry date) and receive a short link that redirects to the original address.

## Features

- Shorten any `http://` / `https://` URL to a 6-character code
- Optional expiry date — expired links return a 404
- Duplicate detection (same URL + expiry returns the existing short link)
- Security headers out of the box (CSP with nonces, HSTS, X-Frame-Options, etc.)

## Requirements

- PHP **8.2+** with the `pdo_sqlite` extension
- [Composer](https://getcomposer.org/)
- A web server (Apache/nginx) for production, or PHP's built-in server for development

Only `public/` should ever be exposed by the web server. The SQLite database lives in `app/Database/`, outside the document root.

## Setup (development)

1. **Clone and install dependencies**

   ```bash
   git clone https://github.com/nickmohr/TFLI-App.git
   cd TFLI-App
   composer install
   ```

2. **Create your configuration**

   ```bash
   cp app/config.example.php app/config.php
   ```

   The defaults (`APP_ENV = 'development'`) work as-is for local use.

3. **Run the built-in server**

   ```bash
   php -S localhost:8000 -t public/
   ```

   Open <http://localhost:8000>. The SQLite database and schema are created automatically on first request.

## Configuration

All settings live in `app/config.php` (copied from `config.example.php`):

| Constant      | Description                                                                                     |
| ------------- | ----------------------------------------------------------------------------------------------- |
| `APP_ENV`     | `development` (errors displayed, base URL taken from the request) or `production` (errors hidden, base URL taken from `APP_URL`) |
| `APP_URL`     | Absolute base URL used to build short links in production, e.g. `https://yourdomain.com`        |
| `DB_DSN`      | PDO DSN for the SQLite database (default: `app/Database/database.sqlite`)                       |
| `APP_NAME`    | Display name shown in the UI                                                                    |
| `APP_VERSION` | Application version string                                                                      |


## Routes

| Method | Path          | Description                                    |
| ------ | ------------- | ---------------------------------------------- |
| GET    | `/`           | URL shortening form                            |
| POST   | `/`           | Create a short URL (JSON response)             |
| GET    | `/url/{code}` | Redirect to the original URL (302), 404 if missing or expired |

## Development tooling

```bash
composer phpstan     # Static analysis (PHPStan, level 8)
composer cs-dryrun   # Show coding-style violations (PHP-CS-Fixer)
composer cs-fix      # Fix coding style
```

# Laundry Shop Management System

Laravel application for managing laundry orders, customers, payments, employees, and sales reports.

## Requirements

- PHP 8.3 or newer with the extensions required by Laravel and the selected database driver
- Composer 2
- Node.js 22 and npm
- MySQL/MariaDB or PostgreSQL
- `pdo_sqlite` to run the automated test suite

## Local setup

1. Install PHP dependencies and create the local environment file:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

2. Create a local database and set `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`. Keep `APP_ENV=local`, `APP_DEBUG=true`, and the configured `APP_TIMEZONE` for local development.

3. Apply the schema and install front-end dependencies:

   ```powershell
   php artisan migrate
   npm ci
   npm run build
   php artisan storage:link
   ```

4. Start the application:

   ```powershell
   php artisan serve
   ```

## Demo data

Demo users and sample orders are enabled by default outside production. Set `SEED_DEMO_DATA=false` in `.env` to seed only the service catalog. Production always skips demo users and sample records, even if `SEED_DEMO_DATA=true`. Run:

```powershell
php artisan db:seed
```

The seeder deliberately skips demo users and sample records whenever `APP_ENV=production`. Demo credentials are fixed for local demonstration only; never use them on a public deployment. The service catalog can be seeded independently with `php artisan db:seed --class=ServiceSeeder`.

## Tests and checks

The test configuration uses an in-memory SQLite database. Enable PHP's `pdo_sqlite` extension before running:

```powershell
php artisan test --compact
vendor/bin/pint --dirty --format agent
composer audit --locked
```

## Render deployment

The repository includes a Docker-based Render web service definition in `render.yaml`. Configure the PostgreSQL connection values and a secret `APP_KEY` in Render. Keep `APP_DEBUG=false` in production. The container runs migrations at startup; take a database backup before deploying schema changes.

Two production capabilities still need an explicit Render infrastructure decision:

- The storage-fee command is scheduled hourly in Laravel, but `render.yaml` does not define a Render Cron Job. Fees will not accrue automatically until a scheduler is configured.
- Profile pictures use Laravel's local public disk. Render's service filesystem is ephemeral unless persistent storage is configured, so uploaded pictures may be lost on a restart or redeploy.

## Integrations

- Email uses the configured Laravel mailer. Configure production mail credentials before relying on employee invitations, password reset codes, or payment receipts.
- Ready-for-pickup SMS uses TextBee. Set `TEXTBEE_API_KEY` and, if needed, `TEXTBEE_DEVICE_ID` in the environment. Without an API key, the application logs that the SMS was skipped.

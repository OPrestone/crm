# CRM - Laravel Application

## Overview
A CRM (Customer Relationship Management) web application built with Laravel 11 (PHP 8.2).

## Architecture
- **Framework**: Laravel 11
- **Language**: PHP 8.2
- **Package Manager**: Composer
- **Database**: SQLite (default, file at `database/database.sqlite`)
- **Frontend Build**: Vite (Node.js)

## Project Structure
- `app/` - Application code (Models, Controllers, etc.)
- `bootstrap/` - Framework bootstrap files
- `config/` - Configuration files
- `database/` - Migrations, factories, seeders, and SQLite database
- `public/` - Web root (index.php, assets)
- `resources/` - Views, CSS, JS source files
- `routes/` - Route definitions (web.php, api.php, etc.)
- `storage/` - Logs, cached files, uploaded files
- `tests/` - Application tests
- `vendor/` - Composer dependencies

## Running the Application
The app is served via `php artisan serve --host=0.0.0.0 --port=5000`.

## Environment
- Copy `.env.example` to `.env` to set up environment variables
- `APP_KEY` is pre-generated
- Database uses SQLite by default

## Deployment
Configured for autoscale deployment running `php artisan serve --host=0.0.0.0 --port=5000`.

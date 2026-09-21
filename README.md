# Fully-Featured Personal Expense Tracker

PHP expense tracker upgraded from the file-based MVC app with **Doctrine ORM**, a **custom DI container**, **PHPMailer** (SMTP or file-log fallback), **PHPUnit**, **xDebug3** setup notes, and a **REST API**.

## Requirements

- PHP 8.1+
- Composer
- XAMPP (Apache + MySQL)
- MySQL database named `expenses` (user `root`, empty password by default)

## Setup

1. Open a terminal in this project folder and install dependencies:

   ```bash
   composer install
   ```

2. Create the MySQL database (phpMyAdmin or MySQL CLI):

   ```sql
   CREATE DATABASE expenses CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Confirm DB settings in `config/config.php` (`root` / empty password / dbname `expenses`).

4. Create tables with Doctrine:

   ```bash
   php bin/doctrine.php orm:schema-tool:create
   ```

5. Seed the demo user:

   ```bash
   php bin/seed.php
   ```

6. Start Apache (and MySQL) in XAMPP.

7. Open:

   `http://localhost/Fully-Featured-Personal-Expense-Tracker/public/`

### Demo login

| Username | Password     |
|----------|--------------|
| `admin`  | `password123` |

## Features

- MVC web UI: login, expense list / create / edit / delete, monthly & category reports
- Doctrine entities + repositories for `users` and `expenses`
- Custom `App\Core\Container` wires EntityManager, repositories, mailer, validators, controllers
- Email: monthly summary (Reports page) and over-limit alerts after save (limit in config)
- REST JSON API for expense CRUD
- PHPUnit coverage for entities, repositories, services, container, helpers

## Email configuration

In `config/config.php` under `mail`:

- Leave **`host` empty** (default) → emails are appended to `storage/mail.log` (offline-friendly).
- Set SMTP `host`, `port`, `username`, `password`, `from`, and `to` to send with PHPMailer.

Default expense limit for alerts: `expense_limit` (1000.00).

## REST API

Base URL: `http://localhost/Fully-Featured-Personal-Expense-Tracker/public/api.php`

Log in through the web UI first (session cookie required).

| Method | URL | Action |
|--------|-----|--------|
| GET | `api.php?resource=expenses` | List all |
| GET | `api.php?resource=expenses&id=1` | Get one |
| POST | `api.php?resource=expenses` | Create (JSON body) |
| PUT / PATCH | `api.php?resource=expenses&id=1` | Update (JSON body) |
| DELETE | `api.php?resource=expenses&id=1` | Delete |

Example create body:

```json
{
  "date": "2026-09-20",
  "category": "Food",
  "item": "Lunch",
  "amount": "12.50"
}
```

Example with curl (after logging in and copying the PHPSESSID cookie):

```bash
curl -X GET "http://localhost/Fully-Featured-Personal-Expense-Tracker/public/api.php?resource=expenses" ^
  -H "Cookie: PHPSESSID=your_session_id"
```

## Unit tests

```bash
composer install
vendor\bin\phpunit
```

Tests use an in-memory SQLite database for repository cases (no MySQL required for PHPUnit).

## xDebug3 (XAMPP / Windows)

1. Confirm `php_xdebug.dll` is present under `C:\xampp\php\ext\`.
2. Edit `C:\xampp\php\php.ini` and enable:

   ```ini
   zend_extension=xdebug
   xdebug.mode=debug,develop
   xdebug.start_with_request=yes
   xdebug.client_host=127.0.0.1
   xdebug.client_port=9003
   ```

3. Restart Apache.
4. In Cursor / VS Code, install a PHP Debug extension, listen on port **9003**, set breakpoints in controllers or services, then load a page in the browser.
5. Use the debug sidebar for **watches**, **locals**, and the **call stack** (stack traces) while stepping.
6. For performance checks, temporarily set `xdebug.mode=profile` or use `develop` mode notices to spot slow paths; prefer repository queries over loading everything into PHP when datasets grow.

Verify the extension:

```bash
php -v
```

You should see `with Xdebug v3.x.x`.

## Project structure

```text
public/index.php       Web front controller
public/api.php         REST API entry
config/config.php      DB, mail, categories, limit
src/Core/              Container, Helper
src/Entity/            Expense, User
src/Repository/        Doctrine repositories
src/Service/           Validator, reports, email
src/Controller/        Web + API controllers
views/                 PHP templates
bin/doctrine.php       Schema tool
bin/seed.php           Admin user seed
tests/                 PHPUnit tests
storage/mail.log       Email fallback output
```

## Web routes

| Page | Description |
|------|-------------|
| `?page=login` | Sign in |
| `?page=logout` | Sign out |
| `?page=expenses` | List expenses |
| `?page=expenses/create` | Add expense |
| `?page=expenses/edit&id=N` | Edit expense |
| `?page=expenses/delete` | Delete (POST) |
| `?page=reports` | Reports |
| `?page=email/monthly-summary` | Send monthly summary (POST) |

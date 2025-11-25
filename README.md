# mer-florist-website

Mer is a lightweight e-commerce website for a florist built with PHP, HTML, CSS, and JavaScript. It provides a simple online storefront for browsing bouquets, managing a cart, user authentication, and an admin area for managing flowers, users and messages.


## Features

- Browse product catalogue and search bouquets
- Add items to cart and simple checkout page
- User registration, login and profile (avatar upload)
- Admin area: add/edit flowers, manage users and messages
- Uses SQLite/MySQL via PDO (configurable in `Config/config.php`)

## Prerequisites

- PHP 7.4+ (with PDO and fileinfo extensions)
- A web server (Apache recommended via XAMPP, WAMP or similar)
- MySQL or MariaDB (or modify config to use SQLite)
- Composer (optional, not required for this project)

## Installation

1. Clone or copy the repository into your web server document root. Example using XAMPP on Windows:

```bash
cd C:/xampp/htdocs
git clone https://github.com/Sanduni204/mer-florist-website.git mer_ecommerce
```

2. Ensure the project folder is readable and writable for PHP (uploading avatars writes to `Images/avatars/`).

3. Create a database and run the SQL to set up tables. If you don't have an SQL script included, the app attempts to add missing columns on `profile.php` — but it's recommended to import a proper schema if available.

4. Update the database connection in `Config/config.php` with your DB credentials.

## Configuration

- Open `Config/config.php` and set the correct DSN, username and password for your database. Example for MySQL:

```php
$dbHost = '127.0.0.1';
$dbName = 'merdb';
$dbUser = 'root';
$dbPass = '';
$conn = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
```

- Ensure `APPURL` in `includes/header.php` (or `Config/config.php`) uses your local URL, for example `http://localhost/mer_ecommerce/`.

## Run (Local Development)

1. Start your web server (Apache) and database server (MySQL) via XAMPP/WAMP.
2. Place the project folder inside your server's document root (`C:/xampp/htdocs/mer_ecommerce`).
3. Visit the site in your browser at:

```
http://localhost/mer_ecommerce/
```

4. To access admin pages, log in with an admin account. If none exists, create an admin user directly in the `users` table (set `is_admin = 1`).

## Project Structure

Top-level important files and folders:

- `1style.css`, `payment.css` — main styles used by the site
- `1javafile.js` — site JavaScript
- `profile.php` — user profile and avatar upload
- `cart.php`, `add_to_cart.php` — cart functionality
- `auth/` — authentication pages (login/register/reset)
- `admin/` — admin dashboard and management pages
- `Config/config.php` — database connection and configuration
- `Images/` — store images and `Images/avatars/` for uploaded avatars
- `includes/` — `header.php` and `footer.php` used across pages

## Admin / User Accounts

- Admin pages are under the `admin/` folder and require `is_admin` flag in the `users` table.
- To create an admin user manually, insert a user row and set `is_admin = 1`.

## Contributing

- Fork the repository, create a feature branch, and open a pull request.
- Keep changes focused and provide clear commit messages.

## Troubleshooting

- If you see permission errors when uploading images, ensure `Images/avatars/` is writable by the web server user.
- If pages show database errors, double-check credentials in `Config/config.php` and confirm the database has the required tables.








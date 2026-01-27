# PHP_Laravel12_Pulse

A simple Laravel 12 project demonstrating the use of Laravel Pulse for real-time application monitoring.
This project is beginner-friendly and shows how to track requests, database queries, slow responses, and exceptions using Laravel’s built-in Pulse feature.

## Project Overview

Laravel Pulse provides real-time insights into your application’s performance.
This project demonstrates how to enable and use Pulse in a Laravel 12 application without any third-party services.

The goal of this project is to help beginners understand:

* How Laravel Pulse works in Laravel 12
* How to monitor application activity
* How to generate and view Pulse data

## Features

* Laravel 12 framework
* Built-in Laravel Pulse monitoring
* Real-time request tracking
* Database query monitoring
* Slow request detection
* Exception tracking
* Demo routes to generate Pulse data
* Clean and minimal setup

## Tech Stack

* PHP 8.1 or higher
* Laravel 12
* MySQL
* Blade Templates
* Laravel Pulse

## Installation Requirements

Before starting, make sure you have:

* PHP 8.1 or higher
* Composer
* MySQL
* XAMPP or any local server
* Git (optional)

## Project Setup Step by Step

### Step 1: Create the Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Pulse
cd PHP_Laravel12_Pulse
```

### Step 2: Environment Configuration

Open the `.env` file and update your database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_pulse
DB_USERNAME=root
DB_PASSWORD=
```

Create the database `laravel12_pulse` using phpMyAdmin or MySQL CLI.

### Step 3: Publish Pulse Configuration and Migrations

Laravel 12 includes Pulse by default. Publish the required files:

```bash
php artisan vendor:publish --tag=pulse-config
php artisan vendor:publish --tag=pulse-migrations
```

### Step 4: Run Database Migrations

```bash
php artisan migrate
```

This will create all Pulse-related tables in the database.

### Step 5: Start the Pulse Worker

Pulse requires a worker to collect monitoring data.

```bash
php artisan pulse:work
```

Keep this terminal running.

### Step 6: Start the Laravel Server

Open a new terminal and run:

```bash
php artisan serve
```

### Step 7: Access the Pulse Dashboard

Open your browser and visit:

```
http://127.0.0.1:8000/pulse
```

You should now see the Laravel Pulse dashboard.

## Demo Routes for Testing Pulse

Add the following routes in `routes/web.php` to generate Pulse data:

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-query', function () {
    DB::table('users')->get();
    return 'Database query executed';
});

Route::get('/slow-request', function () {
    sleep(3);
    return 'Slow request completed';
});
```

Visit these URLs and refresh `/pulse` to see real-time monitoring data.

## Project Structure

```
PHP_Laravel12_Pulse/
|
├── app/
├── bootstrap/
├── config/
│   └── pulse.php
├── database/
│   └── migrations/
├── routes/
│   └── web.php
├── resources/
│   └── views/
├── public/
├── .env
└── README.md
```

## How Laravel Pulse Works

* Pulse listens to application events
* Performance data is stored in the database
* The Pulse worker processes this data
* The dashboard displays real-time insights
* No external services or APIs are required

## Common Commands Used

```bash
php artisan pulse:work
php artisan pulse:check
php artisan pulse:clear
php artisan pulse:purge
php artisan migrate
php artisan serve
```
## screenshot
<img width="1844" height="958" alt="image" src="https://github.com/user-attachments/assets/3ef63915-7d14-474d-8d8b-2a1945a1e939" />

## Security Note

For production environments, access to the Pulse dashboard should be restricted using authentication and authorization.
This project keeps Pulse open only for local development and learning purposes.

## Learning Outcome

After completing this project, you will understand:

* How to use Laravel Pulse in Laravel 12
* How to monitor application performance
* How to analyze slow requests and database queries
* How to work with Laravel’s built-in monitoring tools

## Author

Mihir Mehta
PHP Laravel Developer

## License

This project is open-source and available for learning and educational purposes.


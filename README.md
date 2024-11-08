# Laravel Vue Product Catalog

A lightweight product catalog built with Laravel and Vue.js, demonstrating best practices in software architecture.

## Features

- Product Management (CRUD operations)
- Category Management with hierarchical structure
- Command Line Interface for product creation
- Web interface with sorting and filtering capabilities
- File upload handling for product images
- Comprehensive automated testing

## Architecture & Patterns

- **Repository Pattern**: Separation of database logic from business logic
- **SOLID Principles**: Focus on maintainable and extensible code
- **DRY (Don't Repeat Yourself)**: Code reusability and maintenance
- **KISS (Keep It Simple, Stupid)**: Straightforward, understandable solutions
- **Clean Architecture**: Proper separation of concerns
- **PSR Standards**: Following PHP coding standards

## Technologies

- PHP 8.2+
- Laravel 11.x
- Vue.js 3.x
- MySQL
- Pest for testing

## Requirements

- PHP >= 8.2+
- MySQL >= 5.7
- Node.js >= 12.x
- Composer
- NPM (pnpm)

## Installation

1. Clone the repository
```bash
git clone https://github.com/mzarhou/laravel-vue-product-catalog.git
cd laravel-vue-product-catalog
```

2. Setup environment file
```bash
cp .env.example .env
```

3. Configure your database in `.env` (or just use ```docker compose up --build -d```)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. Setup Project
```bash
./scripts/init.sh
```
## Usage

### Web Interface

1. Start the development server:
```bash
composer dev
```

2. Visit `http://localhost:8000` in your browser

### CLI Commands

Create a new product via command line:
```bash
php artisan product:create <name> <description> <price> <image> <categories>...
```

## Testing

Run the automated test suite:
```bash
php artisan test
```

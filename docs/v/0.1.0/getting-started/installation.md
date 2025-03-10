# Installation

## Installation via Composer

You can install the Bupple AI Engine package via Composer:

```bash
composer require bupple/laravel-ai-engine
```

## Service Provider Registration

The package will automatically register its service provider in Laravel 11 and above. Or you can add manually in `bootstrap/providers.php`:

```php
return [
    // ...
    Bupple\Engine\Providers\BuppleEngineServiceProvider::class,
],
```

## Facade Registration

The package also provides a facade for easy access. It's automatically registered in Laravel 11 and above.

## Publishing Configuration

Publish the configuration file using the following command:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider"
```

This will create:
- `config/bupple-engine.php` configuration file
- Migration files for the memory database driver (if using database storage)

## Database Setup

If you plan to use the database memory driver, run the migrations:

```bash
php artisan migrate
```

### MongoDB Setup (Optional)

If you want to use MongoDB as your memory storage:

1. Install the MongoDB PHP extension
2. Install the Laravel MongoDB package:
```bash
composer require mongodb/laravel-mongodb
```

3. Configure your MongoDB connection in `config/database.php`
4. Set `mongodb_enabled` to `true` in your `config/bupple-engine.php`

### Redis Setup (Coming Soon)

If you want to use Redis as your memory storage:

1. Install the Redis PHP extension
2. Install Predis:
```bash
composer require predis/predis
```

3. Configure your Redis connection in `config/database.php`

## Environment Configuration

Copy the environment variables from the [Requirements](./requirements.md) page to your `.env` file and set appropriate values.

## Directory Structure

After installation, your project structure will include:

```
config/
  └── bupple-engine.php      # Main configuration file
database/
  └── migrations/
      └── xxxx_xx_xx_create_engine_memory_table.php  # Memory table migration
storage/
  └── app/
      └── engine-memory/     # Default storage location for file driver
```

<!-- ## Verification -->

<!-- To verify the installation, you can run:

```bash
php artisan engine:check
``` -->

<!-- This will check:
- Configuration file presence
- Environment variables
- Database migrations (if applicable)
- API key validity
- Memory driver functionality  -->
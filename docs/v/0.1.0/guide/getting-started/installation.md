# Installation

This guide will walk you through the process of installing and setting up the Bupple Laravel AI Engine in your Laravel application.

## Requirements

Before installing the package, make sure your environment meets the following requirements:

- PHP ^8.2 or ^8.3
- Laravel ^11.0 or ^12.0
- Guzzle ^7.8
- JSON PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension (for database storage)

## Installation Steps

### 1. Install via Composer

Install the package using Composer:

```bash
composer require bupple/laravel-ai-engine
```

### 2. Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider"
```

This will create a `config/bupple-engine.php` file in your application.

### 3. Database Setup

If you plan to use the memory management system with a database (which is recommended), you'll need to:

1. Publish the migrations:
```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider" --tag="bupple-engine-migrations"
```

2. Run the migrations:
```bash
php artisan migrate
```

### 4. MongoDB Support (Optional)

If you want to use MongoDB for memory storage:

1. Install the MongoDB package:
```bash
composer require mongodb/laravel-mongodb
```

2. Update your `.env` file:
```env
BUPPLE_USE_MONGODB=true
```

3. Configure your MongoDB connection in `config/database.php`.

## Service Provider Registration

The package's service provider is automatically registered through Laravel's package auto-discovery. However, if you have disabled auto-discovery, add the following to your `config/app.php` file:

```php
'providers' => [
    // ...
    Bupple\Engine\Providers\BuppleEngineServiceProvider::class,
],

'aliases' => [
    // ...
    'BuppleEngine' => Bupple\Engine\Facades\BuppleEngine::class,
],
```

## Environment Variables

Add the following variables to your `.env` file:

```env
# Default driver selection
BUPPLE_CHAT_DRIVER=openai     # Options: openai, gemini, claude
BUPPLE_MEMORY_DRIVER=openai   # Options: openai, gemini, claude

# OpenAI Configuration
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=1000
OPENAI_ORGANIZATION_ID=       # Optional

# Google Gemini Configuration
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-pro
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1000
GEMINI_PROJECT_ID=            # Optional

# Anthropic Claude Configuration
CLAUDE_API_KEY=your-claude-api-key
CLAUDE_MODEL=claude-3-opus-20240229
CLAUDE_TEMPERATURE=0.7
CLAUDE_MAX_TOKENS=1000

# Database Configuration (Optional)
BUPPLE_USE_MONGODB=false
BUPPLE_DB_CONNECTION=mysql    # Or your preferred connection
```

## Verifying Installation

To verify that the package is installed correctly, you can run:

```php
use Bupple\Engine\Facades\BuppleEngine;

// This should return the package configuration
$config = BuppleEngine::config();

// This should return a chat driver instance
$ai = BuppleEngine::ai();

// This should return a memory manager instance
$memory = BuppleEngine::memory();
```

## Troubleshooting

If you encounter any issues during installation:

1. Clear Laravel's cache:
```bash
php artisan config:clear
php artisan cache:clear
```

2. Verify your composer dependencies:
```bash
composer dump-autoload
```

3. Check that your environment variables are properly set in `.env`

4. Ensure your database configuration is correct

5. If using MongoDB, verify your MongoDB connection

## Next Steps

Now that you have installed the package, proceed to:

1. [Configuration](configuration) - Configure the package for your needs
2. [Basic Usage](../basic-usage) - Learn how to use the package
3. [Core Concepts](../core/ai-providers) - Understand the core concepts

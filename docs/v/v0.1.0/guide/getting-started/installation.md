# Installation

This guide will help you install and set up the Bupple Laravel AI Engine in your Laravel project.

## Requirements

Before installing the package, make sure your environment meets these requirements:

- PHP ^8.1
- Laravel ^11.0|^12.0
- Guzzle ^7.8
- JSON PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension (for database storage)
- MongoDB PHP Extension (optional, for MongoDB storage)

## Installation Steps

### 1. Install via Composer

```bash
composer require bupple/laravel-ai-engine
```

### 2. Publish Configuration

Publish the package configuration file:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider"
```

This will create a `config/bupple-engine.php` file in your config directory.

### 3. Database Setup

If you're using database storage for memory management, publish and run the migrations:

```bash
# Publish migrations
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider" --tag="bupple-engine-migrations"

# Run migrations
php artisan migrate
```

### 4. Environment Configuration

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

### 5. MongoDB Setup (Optional)

If you plan to use MongoDB for memory storage:

1. Install the MongoDB PHP extension
2. Install the MongoDB Laravel package:
   ```bash
   composer require mongodb/laravel-mongodb
   ```
3. Configure MongoDB connection in your `config/database.php`
4. Set `BUPPLE_USE_MONGODB=true` in your `.env` file

## Verification

To verify the installation:

1. Check if the service provider is registered:
   ```php
   // config/app.php
   'providers' => [
       // ...
       Bupple\Engine\Providers\BuppleEngineServiceProvider::class,
   ],
   ```

2. Test the installation:
   ```php
   use Bupple\Engine\Facades\BuppleEngine;
   
   $response = BuppleEngine::ai()->send([
       ['role' => 'user', 'content' => 'Hello!']
   ]);
   ```

## Common Issues

### API Key Configuration

Make sure to obtain API keys from the respective providers:
- [OpenAI API Keys](https://platform.openai.com/api-keys)
- [Google AI Studio](https://makersuite.google.com/app/apikey)
- [Anthropic Console](https://console.anthropic.com/)

### MongoDB Issues

If you encounter MongoDB connection issues:
1. Verify MongoDB service is running
2. Check connection string in `config/database.php`
3. Ensure MongoDB PHP extension is properly installed

### Memory Storage Issues

If memory storage isn't working:
1. Verify migrations have been run
2. Check database connection configuration
3. Ensure proper permissions on storage directories

## Next Steps

- [Configuration Guide](/guide/configuration) - Learn how to configure the package
- [Basic Usage](/examples/basic-usage) - Start using the package
- [Memory Management](/guide/memory-management) - Learn about memory features
- [Streaming](/guide/streaming) - Implement streaming responses 
# Configuration

The Bupple AI Engine configuration is managed through the `config/bupple-engine.php` file and environment variables.

## Engine Configuration

### Default Engine

You can set the default AI engine driver in your `.env` file:

```env
BUPPLE_DEFAULT_ENGINE_DRIVER=openai
```

Supported values: `openai`, `gemini`, `claude`

### OpenAI Configuration

```php
'openai' => [
    'api_key' => env('BUPPLE_ENGINE_OPENAI_API_KEY'),
    'model' => env('BUPPLE_ENGINE_OPENAI_MODEL', 'gpt-4'),
    'temperature' => env('BUPPLE_ENGINE_OPENAI_TEMPERATURE', 0.7),
    'max_tokens' => env('BUPPLE_ENGINE_OPENAI_MAX_TOKENS', 1000),
    'organization_id' => env('BUPPLE_ENGINE_OPENAI_ORGANIZATION_ID', null),
],
```

### Google Gemini Configuration

```php
'gemini' => [
    'api_key' => env('BUPPLE_ENGINE_GEMINI_API_KEY'),
    'model' => env('BUPPLE_ENGINE_GEMINI_MODEL', 'gemini-pro'),
    'temperature' => env('BUPPLE_ENGINE_GEMINI_TEMPERATURE', 0.7),
    'max_tokens' => env('BUPPLE_ENGINE_GEMINI_MAX_TOKENS', 1000),
    'project_id' => env('BUPPLE_ENGINE_GEMINI_PROJECT_ID', null),
],
```

### Anthropic Claude Configuration

```php
'claude' => [
    'api_key' => env('BUPPLE_ENGINE_CLAUDE_API_KEY'),
    'model' => env('BUPPLE_ENGINE_CLAUDE_MODEL', 'claude-3-opus-20240229'),
    'temperature' => env('BUPPLE_ENGINE_CLAUDE_TEMPERATURE', 0.7),
    'max_tokens' => env('BUPPLE_ENGINE_CLAUDE_MAX_TOKENS', 1000),
],
```

## Memory Configuration

### Default Memory Driver

Set the default memory storage driver:

```env
BUPPLE_MEMORY_DRIVER=file
```

Supported values: `file`, `database`, `redis`

### Database Driver Configuration

```php
'database' => [
    'mongodb_enabled' => env('BUPPLE_MEMORY_DB_MONGODB_ENABLED', false),
    'connection' => env('BUPPLE_MEMORY_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
    'table_name' => env('BUPPLE_MEMORY_DB_TABLE_NAME', 'engine_memory'),
],
```

### File Driver Configuration

```php
'file' => [
    'path' => storage_path(env('BUPPLE_MEMORY_FILE_PATH', 'app/engine-memory')),
],
```

### Redis Driver Configuration

```php
'redis' => [
    'connection' => env('BUPPLE_MEMORY_REDIS_CONNECTION', 'redis'),
    'key' => env('BUPPLE_MEMORY_REDIS_KEY', 'engine_memory'),
    'ttl' => env('BUPPLE_MEMORY_REDIS_TTL', 60 * 60 * 24 * 30), // 30 days
],
```

## Configuration Methods

### Runtime Configuration

You can modify configuration at runtime using the `config` method:

```php
use Bupple\Engine\Facades\Engine;

// Get configuration
$config = Engine::config();

// Get specific configuration value
$model = Engine::config('engine.openai.model');

// Set configuration value
Engine::config(['engine.openai.model' => 'gpt-4-turbo']);
```

### Environment Variables

All configuration values can be set through environment variables. The package follows Laravel's environment variable naming convention:

1. All uppercase
2. Words separated by underscores
3. Prefixed with `BUPPLE_`

For example:
- `BUPPLE_DEFAULT_ENGINE_DRIVER`
- `BUPPLE_ENGINE_OPENAI_API_KEY`
- `BUPPLE_MEMORY_DRIVER`

## Configuration Validation

The package validates your configuration during initialization. If any required values are missing or invalid, it will throw appropriate exceptions with descriptive messages. 
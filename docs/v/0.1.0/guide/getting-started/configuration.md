# Configuration

This guide explains all the configuration options available in the Bupple Laravel AI Engine.

## Configuration File

After publishing the package configuration, you'll find the configuration file at `config/bupple-engine.php`. This file contains all the settings for the package.

## Default Drivers

```php
'default' => [
    'chat' => env('BUPPLE_CHAT_DRIVER', 'openai'),
    'memory' => env('BUPPLE_MEMORY_DRIVER', 'openai'),
],
```

These settings determine which drivers to use by default for chat and memory operations:
- `chat`: The default AI provider for chat completions
- `memory`: The default provider for memory embeddings

Available options for both: `openai`, `gemini`, `claude`

## Memory Configuration

```php
'memory' => [
    'openai' => [
        'model' => env('BUPPLE_MEMORY_OPENAI_MODEL', 'gpt-4'),
        'temperature' => env('BUPPLE_MEMORY_OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('BUPPLE_MEMORY_OPENAI_MAX_TOKENS', 1000),
    ],
    'gemini' => [
        'model' => env('BUPPLE_MEMORY_GEMINI_MODEL', 'gemini-pro'),
        'temperature' => env('BUPPLE_MEMORY_GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('BUPPLE_MEMORY_GEMINI_MAX_TOKENS', 1000),
    ],
    'claude' => [
        'model' => env('BUPPLE_MEMORY_CLAUDE_MODEL', 'claude-3-opus-20240229'),
        'temperature' => env('BUPPLE_MEMORY_CLAUDE_TEMPERATURE', 0.7),
        'max_tokens' => env('BUPPLE_MEMORY_CLAUDE_MAX_TOKENS', 1000),
    ],
],
```

These settings configure how each provider handles memory operations:
- `model`: The model to use for memory embeddings
- `temperature`: Controls randomness in responses (0.0 to 1.0)
- `max_tokens`: Maximum tokens for memory operations

## OpenAI Configuration

```php
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'model' => env('OPENAI_MODEL', 'gpt-4'),
    'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
    'organization_id' => env('OPENAI_ORGANIZATION_ID', null),
],
```

OpenAI-specific configuration:
- `api_key`: Your OpenAI API key
- `model`: The model to use (e.g., 'gpt-4', 'gpt-3.5-turbo')
- `temperature`: Controls response randomness
- `max_tokens`: Maximum tokens per response
- `organization_id`: Optional OpenAI organization ID

## Google Gemini Configuration

```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-pro'),
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
    'project_id' => env('GEMINI_PROJECT_ID', null),
],
```

Gemini-specific configuration:
- `api_key`: Your Google Gemini API key
- `model`: The model to use (e.g., 'gemini-pro')
- `temperature`: Controls response randomness
- `max_tokens`: Maximum tokens per response
- `project_id`: Optional Google Cloud project ID

## Anthropic Claude Configuration

```php
'claude' => [
    'api_key' => env('CLAUDE_API_KEY'),
    'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
    'temperature' => env('CLAUDE_TEMPERATURE', 0.7),
    'max_tokens' => env('CLAUDE_MAX_TOKENS', 1000),
],
```

Claude-specific configuration:
- `api_key`: Your Anthropic API key
- `model`: The model to use (e.g., 'claude-3-opus-20240229')
- `temperature`: Controls response randomness
- `max_tokens`: Maximum tokens per response

## Database Configuration

```php
'database' => [
    'use_mongodb' => env('BUPPLE_USE_MONGODB', false),
    'connection' => env('BUPPLE_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
    'memory_table' => env('BUPPLE_MEMORY_TABLE', 'engine_memory'),
    'chat_table' => env('BUPPLE_CHAT_TABLE', 'engine_chats'),
],
```

Database-related configuration:
- `use_mongodb`: Whether to use MongoDB for storage
- `connection`: Database connection to use
- `memory_table`: Table name for storing memories (defaults to 'engine_memory')
- `chat_table`: Table name for storing chat history (defaults to 'engine_chats')

## Environment Variables

For better security and flexibility, it's recommended to use environment variables. Here's a complete list of available variables:

```env
# Default Drivers
BUPPLE_CHAT_DRIVER=openai
BUPPLE_MEMORY_DRIVER=openai

# OpenAI Configuration
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=1000
OPENAI_ORGANIZATION_ID=

# Memory-specific OpenAI Configuration
BUPPLE_MEMORY_OPENAI_MODEL=gpt-4
BUPPLE_MEMORY_OPENAI_TEMPERATURE=0.7
BUPPLE_MEMORY_OPENAI_MAX_TOKENS=1000

# Gemini Configuration
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-pro
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1000
GEMINI_PROJECT_ID=

# Memory-specific Gemini Configuration
BUPPLE_MEMORY_GEMINI_MODEL=gemini-pro
BUPPLE_MEMORY_GEMINI_TEMPERATURE=0.7
BUPPLE_MEMORY_GEMINI_MAX_TOKENS=1000

# Claude Configuration
CLAUDE_API_KEY=your-claude-api-key
CLAUDE_MODEL=claude-3-opus-20240229
CLAUDE_TEMPERATURE=0.7
CLAUDE_MAX_TOKENS=1000

# Memory-specific Claude Configuration
BUPPLE_MEMORY_CLAUDE_MODEL=claude-3-opus-20240229
BUPPLE_MEMORY_CLAUDE_TEMPERATURE=0.7
BUPPLE_MEMORY_CLAUDE_MAX_TOKENS=1000

# Database Configuration
BUPPLE_USE_MONGODB=false
BUPPLE_DB_CONNECTION=mysql
BUPPLE_MEMORY_TABLE=engine_memory
BUPPLE_CHAT_TABLE=engine_chats
```

## Runtime Configuration

You can also modify configuration at runtime:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get current configuration
$config = BuppleEngine::config();

// Get specific configuration value
$model = BuppleEngine::config('openai.model');

// Get memory configuration
$memoryConfig = BuppleEngine::memory()->getConfig();
```

## Next Steps

Now that you've configured the package, you can:

1. Learn about [Basic Usage](../basic-usage)
2. Explore [AI Providers](../core/ai-providers)
3. Understand [Memory Management](../core/memory-management)

# Configuration

This guide explains how to configure the Bupple Laravel AI Engine for your application.

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

## Configuration File

After publishing the configuration file, you can find it at `config/bupple-engine.php`:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Drivers
    |--------------------------------------------------------------------------
    |
    | Here you can specify the default drivers for chat and memory operations.
    |
    */
    'default' => [
        'chat' => env('BUPPLE_CHAT_DRIVER', 'openai'),
        'memory' => env('BUPPLE_MEMORY_DRIVER', 'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization_id' => env('OPENAI_ORGANIZATION_ID'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'temperature' => (float) env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'project_id' => env('GEMINI_PROJECT_ID'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => (int) env('GEMINI_MAX_TOKENS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Anthropic Claude Configuration
    |--------------------------------------------------------------------------
    */
    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
        'temperature' => (float) env('CLAUDE_TEMPERATURE', 0.7),
        'max_tokens' => (int) env('CLAUDE_MAX_TOKENS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Storage Configuration
    |--------------------------------------------------------------------------
    */
    'memory' => [
        'use_mongodb' => (bool) env('BUPPLE_USE_MONGODB', false),
        'connection' => env('BUPPLE_DB_CONNECTION', 'mysql'),
        'table' => 'memories',
    ],
];
```

## Provider Configuration

### OpenAI

1. Get your API key from [OpenAI API Keys](https://platform.openai.com/api-keys)
2. Optional: Get your Organization ID from [OpenAI Settings](https://platform.openai.com/account/org-settings)
3. Configure the model and parameters:
   ```env
   OPENAI_MODEL=gpt-4           # or gpt-3.5-turbo
   OPENAI_TEMPERATURE=0.7       # 0.0 to 1.0
   OPENAI_MAX_TOKENS=1000
   ```

### Google Gemini

1. Get your API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Optional: Set up a project in [Google Cloud Console](https://console.cloud.google.com)
3. Configure the model and parameters:
   ```env
   GEMINI_MODEL=gemini-pro
   GEMINI_TEMPERATURE=0.7
   GEMINI_MAX_TOKENS=1000
   ```

### Anthropic Claude

1. Get your API key from [Anthropic Console](https://console.anthropic.com)
2. Configure the model and parameters:
   ```env
   CLAUDE_MODEL=claude-3-opus-20240229
   CLAUDE_TEMPERATURE=0.7
   CLAUDE_MAX_TOKENS=1000
   ```

## Memory Storage

### MySQL/PostgreSQL

Default configuration using your Laravel database:

```env
BUPPLE_USE_MONGODB=false
BUPPLE_DB_CONNECTION=mysql
```

### MongoDB

1. Install the MongoDB package:
   ```bash
   composer require mongodb/laravel-mongodb
   ```

2. Configure MongoDB in `config/database.php`:
   ```php
   'mongodb' => [
       'driver' => 'mongodb',
       'host' => env('MONGO_DB_HOST', '127.0.0.1'),
       'port' => env('MONGO_DB_PORT', 27017),
       'database' => env('MONGO_DB_DATABASE', 'your_database'),
       'username' => env('MONGO_DB_USERNAME', ''),
       'password' => env('MONGO_DB_PASSWORD', ''),
   ],
   ```

3. Enable MongoDB in `.env`:
   ```env
   BUPPLE_USE_MONGODB=true
   BUPPLE_DB_CONNECTION=mongodb
   ```

## Runtime Configuration

You can override configuration at runtime:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Override model
$response = BuppleEngine::ai()
    ->withModel('gpt-4')
    ->send($messages);

// Override temperature
$response = BuppleEngine::ai()
    ->withTemperature(0.9)
    ->send($messages);

// Override max tokens
$response = BuppleEngine::ai()
    ->withMaxTokens(2000)
    ->send($messages);
```

## Best Practices

1. **API Keys**:
   - Never commit API keys to version control
   - Use environment variables for sensitive data
   - Consider using Laravel's encrypted environment files

2. **Model Selection**:
   - Use appropriate models for your use case
   - Consider cost implications
   - Monitor token usage

3. **Memory Storage**:
   - Choose storage based on your needs
   - Implement proper indexing
   - Monitor storage growth

4. **Temperature Settings**:
   - Lower for factual responses
   - Higher for creative content
   - Test different values for your use case

## Troubleshooting

### Common Issues

1. **API Key Issues**:
   ```php
   try {
       $response = BuppleEngine::ai()->send($messages);
   } catch (\Exception $e) {
       if (str_contains($e->getMessage(), 'API key')) {
           // Handle API key issues
       }
   }
   ```

2. **Model Not Found**:
   ```php
   try {
       $response = BuppleEngine::ai()
           ->withModel('nonexistent-model')
           ->send($messages);
   } catch (\Exception $e) {
       // Handle model not found
   }
   ```

3. **Database Connection**:
   ```php
   try {
       $memory = BuppleEngine::memory();
       $memory->getMessages();
   } catch (\Exception $e) {
       // Handle database connection issues
   }
   ```

### Verification

Test your configuration:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Test AI provider
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'Test message']
]);

// Test memory storage
$memory = BuppleEngine::memory();
$memory->setParent('test', 1);
$memory->addMessage('user', 'Test message');
$messages = $memory->getMessages();
``` 
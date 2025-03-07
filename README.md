# Bupple Engine Package

A PHP package that provides the Bupple Engine service with Laravel support, featuring multiple AI providers.

## Installation

You can install the package via composer:

```bash
composer require bupple/engine
```

## Configuration

1. The package will automatically register its service provider.

2. Publish the configuration file and migrations:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider" --tag="config"
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider" --tag="migrations"
php artisan migrate
```

3. Add the following environment variables to your `.env` file:

```env
# Default driver
BUPPLE_CHAT_DRIVER=openai # or gemini, claude

# OpenAI Configuration
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=1000

# Gemini Configuration
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-pro
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1000

# Claude Configuration
CLAUDE_API_KEY=your-claude-api-key
CLAUDE_MODEL=claude-3-opus-20240229
CLAUDE_TEMPERATURE=0.7
CLAUDE_MAX_TOKENS=1000
```

## Usage

### Using the Facade

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get the default driver (from config)
$chat = BuppleEngine::driver();

// Or specify a driver
$chat = BuppleEngine::driver('openai');
$chat = BuppleEngine::driver('gemini');
$chat = BuppleEngine::driver('claude');

// Regular chat
$response = $chat->chat([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Streaming chat
foreach ($chat->stream([
    ['role' => 'user', 'content' => 'Hello!']
]) as $chunk) {
    echo $chunk['content'];
}

// Using memory
$memory = BuppleEngine::memory($yourModel, 'openai');
$memory->addUserMessage('Hello!');
$memory->addAssistantMessage('Hi there!');
$messages = $memory->getMessages();
```

### Using Dependency Injection

```php
use Bupple\Engine\BuppleEngine;

class YourController
{
    public function example(BuppleEngine $engine)
    {
        $chat = $engine->driver('openai');
        $response = $chat->chat([
            ['role' => 'user', 'content' => 'Hello!']
        ]);
        
        $memory = $engine->memory($yourModel, 'openai');
        $memory->addUserMessage('Hello!');
    }
}
```

## Available Drivers

### OpenAI Driver
- Standard OpenAI chat format
- Supports GPT-3.5, GPT-4, and other OpenAI models
- Full streaming support
- Multi-modal content support

### Gemini Driver
- Google's Gemini Pro and Vision models
- Automatic message format conversion
- Streaming support
- Multi-modal content support

### Claude Driver
- Anthropic's Claude models
- XML-like formatting for media content
- Streaming support
- Multi-modal content support

## Memory System

All drivers come with a memory system that supports:
- Message history persistence
- Multi-modal content (text, images, audio, video)
- Parent model association
- Message metadata
- Conversation clearing

## Features

- Multiple AI provider support:
  - OpenAI
  - Google Gemini
  - Anthropic Claude
- Laravel integration with Facade support
- Streaming responses
- Memory system with database persistence
- Multi-modal content support
- Clean, extensible architecture
- Easy driver switching
- Comprehensive configuration options

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 
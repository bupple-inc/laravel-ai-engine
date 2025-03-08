# Bupple Laravel AI Engine

<p align="center">
    <img src="https://framerusercontent.com/images/CnM2ZH7e8kIXeOBCOJ7CnBzI4A.png" alt="Bupple" width="120"/>
</p>

A powerful Laravel package that provides a unified interface for multiple AI providers (OpenAI, Google Gemini, and Anthropic Claude) with built-in memory management and streaming capabilities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bupple/laravel-ai-engine.svg?style=flat-square)](https://packagist.org/packages/bupple/laravel-ai-engine)
[![Total Downloads](https://img.shields.io/packagist/dt/bupple/laravel-ai-engine.svg?style=flat-square)](https://packagist.org/packages/bupple/laravel-ai-engine)
[![License](https://img.shields.io/packagist/l/bupple/laravel-ai-engine.svg?style=flat-square)](https://packagist.org/packages/bupple/laravel-ai-engine)

## 🎯 Why We Built This

In today's rapidly evolving AI landscape, developers and companies face significant challenges:
- Managing multiple AI provider integrations
- Handling complex streaming implementations
- Building reliable memory management systems
- Ensuring consistent response formats
- Dealing with provider-specific quirks

The Bupple Laravel AI Engine solves these challenges by providing:
- A unified, provider-agnostic interface
- Built-in streaming with SSE support
- Robust memory management
- Consistent response formatting
- Production-ready error handling

Whether you're building a chatbot, content generation system, or AI-powered application, this package helps you focus on building features rather than wrestling with AI provider integrations.

## 🚧 Development Status

> **Important Notice**: This package is currently under heavy maintenance and active development. A significantly enhanced version 1.0.0 is scheduled for release on May 15st, 2024.

### 📅 Roadmap to v1.0.0

We're working hard to bring you the most comprehensive AI Engine for Laravel. Here's what's coming:

#### 🎯 Upcoming Features (March 15 - April 15)
- [ ] Enhanced streaming performance with WebSocket support
- [ ] Advanced rate limiting and quota management
- [ ] Automatic failover between AI providers
- [ ] Improved error handling and retry mechanisms
- [ ] Real-time analytics and usage monitoring
- [ ] Batch processing capabilities
- [ ] Custom model fine-tuning support

#### 🔨 Under Development (March 5-15)
- [ ] Advanced caching system for responses
- [ ] Multi-tenant support
- [ ] Enhanced security features
- [ ] Performance optimizations
- [ ] Extended provider-specific features
- [ ] Comprehensive test coverage
- [ ] API documentation improvements

#### 🌟 Already Implemented
- [x] Basic AI provider integration (OpenAI, Gemini, Claude)
- [x] Memory management system
- [x] SSE streaming support
- [x] MongoDB integration
- [x] Parent context support
- [x] Basic error handling

### 🔄 Weekly Updates
We're committed to regular updates and improvements. Follow our progress:
- Every Monday: New features and enhancements
- Every Wednesday: Bug fixes and optimizations
- Every Friday: Documentation updates

### 🤝 Early Adopters
We value our early adopters! If you're using the package in production, please:
1. Star the repository to show your support
2. Report any issues you encounter
3. Join our discussions for feature requests
4. Share your use cases and feedback

The current version is stable for basic use cases, but we recommend staying updated with the latest releases for new features and improvements.

## Features

- 🤖 Support for multiple AI providers:
  - OpenAI (GPT-4, GPT-3.5)
  - Google Gemini
  - Anthropic Claude
- 💾 Built-in memory management system
- 🔄 Server-Sent Events (SSE) support for streaming responses
- 🗄️ MongoDB support for memory storage
- 🎯 Parent context support for memory management
- ⚡ Facade and dependency injection support
- 🛠️ Extensive configuration options

## Requirements

- PHP ^8.1
- Laravel ^11.0|^12.0
- Guzzle ^7.8
- JSON PHP Extension

## Installation

1. Install the package via Composer:

```bash
composer require bupple/laravel-ai-engine
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider"
```

3. If you're using database memory storage, publish and run the migrations:

```bash
php artisan vendor:publish --provider="Bupple\Engine\Providers\BuppleEngineServiceProvider" --tag="bupple-engine-migrations"
php artisan migrate
```

## Configuration

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

## Basic Usage

### Chat Completion

```php
use Bupple\Engine\Facades\BuppleEngine;

// Simple chat completion
$response = BuppleEngine::ai()->send([
    [
        'role' => 'user',
        'content' => 'Hello!'
    ]
]);

// Using a specific provider
$response = BuppleEngine::ai('openai')->send([
    [
        'role' => 'system',
        'content' => 'You are a helpful assistant'
    ],
    [
        'role' => 'user',
        'content' => 'Hello!'
    ]
]);

// Streaming chat completion
foreach (BuppleEngine::ai()->stream([
    ['role' => 'user', 'content' => 'Hello!']
]) as $chunk) {
    echo $chunk['content'];
}
```

### Memory Management

```php
use Bupple\Engine\Facades\BuppleEngine;

// Initialize
$ai = BuppleEngine::ai('openai');
$memory = BuppleEngine::memory();

// Set parent context (required)
$memory->setParent(User::class, auth()->id());
// OR
$memory->setParent('conversation', $conversationId);

// Add messages to memory
$memory->addMessage('user', 'What is the capital of France?');

// Get chat history and use it
$messages = $memory->getMessages();
$response = $ai->send($messages);

// Store the response
$memory->addMessage('assistant', $response['content']);
```

### Using Dependency Injection

```php
use Bupple\Engine\BuppleEngine;

class YourController
{
    public function __construct(private BuppleEngine $engine)
    {
    }

    public function someMethod()
    {
        // Chat completion
        $response = $this->engine->ai()->send([
            ['role' => 'user', 'content' => 'Hello!']
        ]);

        // Memory operations
        $memory = $this->engine->memory();
        $memory->setParent(User::class, auth()->id());
        $memory->addMessage('user', 'Hello!');
    }
}
```

## Advanced Features

### Server-Sent Events (SSE)

The package includes a powerful SSE implementation for real-time streaming:

```php
use Bupple\Engine\Facades\BuppleEngine;

return response()->stream(function () {
    $sse = BuppleEngine::sse();
    
    // Configure SSE (optional)
    $sse->setRetryTimeout(5000)  // Set retry timeout to 5 seconds
        ->setEventType('ai-response')  // Custom event type
        ->setAutoFlush(true)  // Auto flush after each message
        ->setHeaders([  // Custom headers
            'Access-Control-Allow-Origin' => '*'
        ]);

    // Start the SSE stream
    $sse->start();

    // Basic streaming
    foreach (BuppleEngine::ai()->stream([
        ['role' => 'user', 'content' => 'Hello!']
    ]) as $chunk) {
        $sse->send($chunk['content']);
    }

    // Advanced streaming with custom events
    $sse->send(
        data: ['message' => 'Processing...'],
        id: 'msg-1',
        eventType: 'status'
    );

    // Send multiple messages in batch
    $sse->sendBatch([
        ['data' => 'First chunk', 'id' => '1'],
        ['data' => 'Second chunk', 'id' => '2', 'event' => 'custom'],
    ]);

    // Keep connection alive
    $sse->keepAlive('Still processing...');

    // Handle errors
    try {
        // Your code
    } catch (\Exception $e) {
        $sse->sendError('An error occurred', 500);
    }

    // End the stream
    $sse->end('All done!');
});
```

#### SSE Features

- **Custom Event Types**: Send different types of events (`setEventType()`)
- **Message IDs**: Track message sequence (`setId()`)
- **Retry Timeout**: Configure reconnection attempts (`setRetryTimeout()`)
- **Custom Headers**: Add CORS or other headers (`setHeaders()`)
- **Batch Messages**: Send multiple messages efficiently (`sendBatch()`)
- **Keep-Alive**: Prevent connection timeouts (`keepAlive()`)
- **Error Handling**: Structured error messages (`sendError()`)
- **Auto-Flush Control**: Optimize output buffering (`setAutoFlush()`)

### AI Providers

Each AI provider (OpenAI, Gemini, Claude) returns responses in a consistent format:

```php
[
    'role' => 'assistant',
    'content' => 'The response text',
    'model' => 'The model used (e.g., gpt-4, gemini-pro)',
    'usage' => [
        // Provider-specific usage data
    ],
]
```

#### OpenAI Features
```php
$response = BuppleEngine::ai('openai')->send([
    ['role' => 'system', 'content' => 'You are a helpful assistant'],
    ['role' => 'user', 'content' => 'Hello!']
]);
```

#### Gemini Features
```php
$response = BuppleEngine::ai('gemini')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

#### Claude Features
```php
$response = BuppleEngine::ai('claude')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

### Memory Management with Context

```php
use Bupple\Engine\Facades\BuppleEngine;

class ChatController
{
    public function chat(Request $request)
    {
        $ai = BuppleEngine::ai('openai');
        $memory = BuppleEngine::memory();

        // Set conversation context
        $memory->setParent('conversation', $request->conversation_id);
        // OR for user-specific context
        $memory->setParent(User::class, auth()->id());

        // Add user message to memory
        $memory->addMessage('user', $request->message);

        // Get conversation history and generate response
        $messages = $memory->getMessages();
        $response = $ai->send($messages);

        // Store AI response in memory
        $memory->addMessage('assistant', $response['content']);

        return $response;
    }
}
```

## MongoDB Support

1. Install the MongoDB package:
```bash
composer require mongodb/laravel-mongodb
```

2. Enable MongoDB in your `.env`:
```env
BUPPLE_USE_MONGODB=true
```

## Best Practices

1. Always handle API errors and rate limits
2. Use environment variables for sensitive credentials
3. Implement proper error handling
4. Consider implementing caching for frequent responses
5. Monitor your API usage and costs

## Troubleshooting

If you encounter issues:

1. Verify API keys in `.env`
2. Check database configuration
3. Clear Laravel cache:
```bash
php artisan config:clear
php artisan cache:clear
```
4. Verify service provider registration
5. Check Laravel logs

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email dev@bupple.io instead of using the issue tracker.

## Credits

- [Bupple's Core Development Team](https://bupple.io/about-us)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Error Handling

The package includes comprehensive error handling:

```php
try {
    $response = BuppleEngine::ai('openai')->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (\RuntimeException $e) {
    // Handle API errors
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
} 
```

## 🌟 About Bupple

Bupple is more than just a company – we're the architects of a new era in social media management. As the creators of the "Swiss Army Knife for Social Content," we've developed a comprehensive AI ecosystem that's revolutionizing how businesses, creators, and agencies approach content creation.

### 🚀 Our Vision
We believe in a world where creating compelling social media content shouldn't take days of work or years of skill. Through our innovative AI Engine, we're making this vision a reality, enabling anyone to create professional-grade content in minutes.

### 🔄 The AI Cycle
Our Laravel AI Engine sits at the heart of Bupple's intelligent content ecosystem, powering a seamless cycle of:
- **Ideation**: AI-driven content brainstorming and trend analysis
- **Creation**: Automated content generation across multiple formats
- **Optimization**: Smart performance analysis and enhancement
- **Learning**: Continuous improvement through usage patterns and results

> **Revolutionizing Social Media with AI-Powered Intelligence**
>
> Welcome to Bupple's Laravel AI Engine – the powerhouse behind the future of social media content creation. Born from an honest story and driven by innovation, Bupple is transforming how the world creates, manages, and optimizes social media content through the power of artificial intelligence.
# API Overview

The Bupple Laravel AI Engine provides a comprehensive API for interacting with various AI providers and managing conversation memory.

## Core Components

### BuppleEngine

The main service class that provides access to all features:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get an AI provider instance
$ai = BuppleEngine::ai();

// Get a specific provider
$openai = BuppleEngine::ai('openai');
$gemini = BuppleEngine::ai('gemini');
$claude = BuppleEngine::ai('claude');

// Get the memory manager
$memory = BuppleEngine::memory();

// Get a specific memory driver
$memory = BuppleEngine::memory()->driver('openai');

// Get the SSE driver
$sse = BuppleEngine::sse();

// Get configuration
$config = BuppleEngine::config();
```

### Chat Drivers

All chat drivers implement the `ChatDriverInterface`:

```php
interface ChatDriverInterface
{
    public function send(array $messages): array;
    public function stream(array $messages): \Generator;
    public function getConfig(): array;
}
```

### Memory Drivers

All memory drivers implement the `MemoryDriverInterface`:

```php
interface MemoryDriverInterface
{
    public function store(string $key, mixed $value): bool;
    public function retrieve(string $key): mixed;
    public function exists(string $key): bool;
    public function delete(string $key): bool;
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function getMessages(): array;
    public function clear(): void;
    public function setParent(string $class, string|int $id): void;
    public function getConfig(): array;
}
```

## Available APIs

### AI Providers
- [OpenAI](providers/openai)
- [Google Gemini](providers/gemini)
- [Anthropic Claude](providers/claude)

### Memory Management
- [Memory Manager](interfaces/memory-interface)
- [Memory Drivers](interfaces/memory-drivers)

### Streaming
- [SSE Driver](interfaces/sse-interface)
- [Streaming Responses](interfaces/streaming)

### Configuration
- [Configuration Options](interfaces/configuration)
- [Environment Variables](interfaces/environment)

## Common Patterns

### Chat Completion

```php
// Simple completion
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// With system message
$response = BuppleEngine::ai()->send([
    [
        'role' => 'system',
        'content' => 'You are a helpful assistant.'
    ],
    [
        'role' => 'user',
        'content' => 'Hello!'
    ]
]);
```

### Memory Usage

```php
// Initialize memory
$memory = BuppleEngine::memory();

// Set context
$memory->setParent('conversation', $conversationId);

// Add messages
$memory->addUserMessage('Hello!');
$memory->addAssistantMessage('Hi there!');

// Get history
$messages = $memory->getMessages();

// Clear history
$memory->clear();
```

### Streaming

```php
// Get streaming response
$stream = BuppleEngine::ai()->stream([
    ['role' => 'user', 'content' => 'Write a story...']
]);

foreach ($stream as $chunk) {
    echo $chunk['content'];
}
```

### Configuration Access

```php
// Get all config
$config = BuppleEngine::config();

// Get specific config
$openaiConfig = BuppleEngine::config('openai');
$model = BuppleEngine::config('openai.model');

// Get memory config
$memoryConfig = BuppleEngine::memory()->getConfig();
```

## Response Formats

### Chat Completion Response

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

### Streaming Response Chunk

```php
[
    'role' => 'assistant',
    'content' => 'Partial response text',
    'done' => false,
]
```

### Memory Message Format

```php
[
    'role' => 'user|assistant|system',
    'content' => 'Message content',
    'type' => 'text|image|audio',
    'metadata' => [
        // Optional metadata
    ],
]
```

## Error Handling

All API methods may throw the following exceptions:

- `\RuntimeException` - For API and operational errors
- `\InvalidArgumentException` - For invalid input
- `\Exception` - For unexpected errors

## Next Steps

1. Explore [Provider APIs](providers/openai)
2. Learn about [Memory Interface](interfaces/memory-interface)
3. Read about [Streaming](interfaces/streaming)

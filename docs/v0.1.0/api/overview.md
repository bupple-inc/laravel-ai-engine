# API Reference

This section provides detailed documentation for all the APIs available in the Bupple Laravel AI Engine.

## Core Components

### BuppleEngine

The main facade and service class that provides access to all functionality:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Access AI functionality
$ai = BuppleEngine::ai();

// Access memory management
$memory = BuppleEngine::memory();
```

### AI Interface

The AI interface provides methods for interacting with AI providers:

```php
interface AiInterface
{
    // Send a chat completion request
    public function send(array $messages): array;
    
    // Stream a chat completion response
    public function stream(array $messages): Generator;
    
    // Configure the model
    public function withModel(string $model): self;
    
    // Set temperature
    public function withTemperature(float $temperature): self;
    
    // Set max tokens
    public function withMaxTokens(int $maxTokens): self;
}
```

### Memory Interface

The memory interface provides methods for managing conversation history:

```php
interface MemoryInterface
{
    // Set the parent context
    public function setParent(string $type, string|int $id): self;
    
    // Add a message to memory
    public function addMessage(string $role, string $content): self;
    
    // Get all messages
    public function getMessages(): array;
    
    // Clear memory
    public function clear(): bool;
}
```

## Available Methods

### AI Methods

#### send()
Send a chat completion request:
```php
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

#### stream()
Stream a chat completion response:
```php
$stream = BuppleEngine::ai()->stream([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

#### withModel()
Set the AI model to use:
```php
$ai = BuppleEngine::ai()->withModel('gpt-4');
```

#### withTemperature()
Set the response temperature:
```php
$ai = BuppleEngine::ai()->withTemperature(0.7);
```

#### withMaxTokens()
Set the maximum tokens for the response:
```php
$ai = BuppleEngine::ai()->withMaxTokens(1000);
```

### Memory Methods

#### setParent()
Set the parent context for memory management:
```php
$memory = BuppleEngine::memory()->setParent(User::class, 1);
```

#### addMessage()
Add a message to memory:
```php
$memory->addMessage('user', 'What is Laravel?');
```

#### getMessages()
Retrieve all messages from memory:
```php
$messages = $memory->getMessages();
```

#### clear()
Clear all messages from memory:
```php
$memory->clear();
```

## Response Formats

### Chat Completion Response

```php
[
    'content' => string,    // The AI's response text
    'role' => string,      // The role (usually 'assistant')
    'model' => string,     // The model used
    'provider' => string,  // The AI provider used
    'usage' => [
        'prompt_tokens' => int,
        'completion_tokens' => int,
        'total_tokens' => int
    ]
]
```

### Streaming Response

Each chunk in the stream contains:
```php
[
    'content' => string,    // The chunk of response text
    'role' => string,      // The role (usually 'assistant')
    'done' => bool         // Whether this is the last chunk
]
```

## Error Handling

The package throws various exceptions that you should handle:

```php
use Bupple\Engine\Exceptions\AiProviderException;
use Bupple\Engine\Exceptions\MemoryException;
use Bupple\Engine\Exceptions\ConfigurationException;

try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    // Handle AI provider errors
} catch (MemoryException $e) {
    // Handle memory management errors
} catch (ConfigurationException $e) {
    // Handle configuration errors
} catch (\Exception $e) {
    // Handle other errors
}
```

## Next Steps

- [BuppleEngine Reference](/api/bupple-engine) - Detailed BuppleEngine documentation
- [AI Interface](/api/ai-interface) - Complete AI interface documentation
- [Memory Interface](/api/memory-interface) - Complete memory interface documentation
- [Configuration Reference](/api/configuration) - All configuration options 
# Memory Interface

The Memory Interface provides a standardized way to interact with the memory management system in the Bupple Laravel AI Engine.

## Interface Definition

```php
interface MemoryDriverInterface
{
    public function addUserMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function addAssistantMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function addSystemMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function getMessages(): array;
    public function clear(): void;
}
```

## Method Descriptions

### addUserMessage()

Adds a user message to the current conversation context.

```php
public function addUserMessage(
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void;
```

Parameters:
- `$content`: The message content
- `$type`: The message type ('text', 'image', 'audio')
- `$metadata`: Additional metadata for the message
- `$messageId`: Optional unique identifier for the message

### addAssistantMessage()

Adds an assistant message to the current conversation context.

```php
public function addAssistantMessage(
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void;
```

Parameters:
- `$content`: The message content
- `$type`: The message type ('text', 'image', 'audio')
- `$metadata`: Additional metadata for the message
- `$messageId`: Optional unique identifier for the message

### addSystemMessage()

Adds a system message to the current conversation context.

```php
public function addSystemMessage(
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void;
```

Parameters:
- `$content`: The message content
- `$type`: The message type ('text', 'image', 'audio')
- `$metadata`: Additional metadata for the message
- `$messageId`: Optional unique identifier for the message

### addMessage()

Adds a message to the current conversation context.

```php
public function addMessage(
    string $role,
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void;
```

Parameters:
- `$role`: The role of the message sender ('user', 'assistant', 'system')
- `$content`: The message content
- `$type`: The message type ('text', 'image', 'audio')
- `$metadata`: Additional metadata for the message
- `$messageId`: Optional unique identifier for the message

### getMessages()

Retrieves all messages in the current conversation context.

```php
public function getMessages(): array;
```

Returns:
- `array`: Array of messages in chronological order

### clear()

Clears all messages in the current conversation context.

```php
public function clear(): void;
```

## Basic Usage

### Message Management

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get memory manager
$memory = BuppleEngine::memory();

// Add messages
$memory->addUserMessage('Hello!');
$memory->addAssistantMessage('Hi there!');
$memory->addSystemMessage('You are a helpful assistant.');

// Add message with metadata
$memory->addUserMessage(
    'How are you?',
    'text',
    ['timestamp' => now()]
);

// Add message with custom ID
$memory->addUserMessage(
    'What is Laravel?',
    'text',
    [],
    'msg_' . Str::uuid()
);

// Get all messages
$messages = $memory->getMessages();

// Clear conversation
$memory->clear();
```

## Error Handling

```php
try {
    $memory->addUserMessage('Hello!');
} catch (\Exception $e) {
    // Handle errors
    Log::error('Memory Error', [
        'message' => $e->getMessage(),
    ]);
}
```

## Best Practices

1. **Message Management**
   - Include relevant metadata
   - Use appropriate message types
   - Consider using custom message IDs for tracking

2. **Error Handling**
   - Validate message content
   - Handle errors appropriately
   - Log errors for debugging

3. **Performance**
   - Clear old messages when needed
   - Monitor memory usage
   - Handle large conversations appropriately

## Next Steps

1. Learn about [Memory Drivers](memory-drivers)
2. Explore [SSE Interface](sse-interface)
3. Read about [Configuration](configuration)

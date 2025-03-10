# Basic Memory Usage

The Bupple AI Engine provides a robust memory management system for storing and retrieving conversation history. Here's how to use the basic memory features.

## Getting Started

First, import the Engine facade:

```php
use Bupple\Engine\Facades\Engine;
```

## Adding Messages

### Adding User Messages

```php
Engine::memory()->addUserMessage('Hello!');
```

### Adding Assistant Messages

```php
Engine::memory()->addAssistantMessage('Hi! How can I help you today?');
```

### Adding System Messages

```php
Engine::memory()->addSystemMessage('You are a helpful assistant.');
```

### Adding Messages with Custom Role

```php
Engine::memory()->addMessage('user', 'Hello!', 'text', [], 'msg_123');
```

The `addMessage` method accepts:
- `role`: The message role ('user', 'assistant', 'system')
- `content`: The message content
- `type`: Message type (default: 'text')
- `metadata`: Additional metadata array
- `messageId`: Optional unique identifier

## Retrieving Messages

### Get All Messages

```php
$messages = Engine::memory()->getMessages();
```

The messages are returned in chronological order.

## Setting Context

Before using memory operations, set the parent context:

```php
// For a specific user
Engine::memory()->setParent(User::class, $userId);

// For a chat session
Engine::memory()->setParent('ChatSession', $sessionId);
```

## Clearing Memory

Clear all messages for the current context:

```php
Engine::memory()->clear();
```

## Switching Memory Drivers

You can switch between different memory drivers:

```php
// Use file driver
$fileMemory = Engine::driver('file');

// Use database driver
$dbMemory = Engine::driver('database');

// Use Redis driver
$redisMemory = Engine::driver('redis');
```

## Message Format

Messages are stored in the following format:

```php
[
    'role' => 'user|assistant|system',
    'content' => 'Message content',
    'type' => 'text',
    'metadata' => [],
    'message_id' => 'optional_unique_id',
    'created_at' => 'timestamp'
]
```

## Error Handling

Basic error handling:

```php
try {
    Engine::memory()->addUserMessage('Hello!');
} catch (\RuntimeException $e) {
    // Handle memory operation errors
    echo "Error: " . $e->getMessage();
}
```

## Next Steps

For more advanced usage, including:
- Custom metadata handling
- Media content storage
- Advanced querying
- Custom memory drivers

See the [Advanced Memory Usage](../advanced-usage/memory.md) guide. 
# Memory Management

The Bupple Laravel AI Engine provides a robust memory management system for storing and retrieving conversation history and other data.

## Overview

The memory system allows you to:
- Store and retrieve conversation history
- Maintain context across multiple interactions
- Organize conversations by parent context
- Support multiple storage backends

## Basic Usage

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get memory manager
$memory = BuppleEngine::memory();

// Get specific memory driver
$memory = BuppleEngine::memory()->driver('openai');

// Add messages
$memory->addUserMessage('Hello!');
$memory->addAssistantMessage('Hi there!');
$memory->addSystemMessage('You are a helpful assistant.');

// Get all messages
$messages = $memory->getMessages();

// Clear conversation history
$memory->clear();
```

## Message Types

The memory system supports different types of messages:

```php
// Text message (default)
$memory->addUserMessage('Hello!');

// Image message
$memory->addUserMessage(
    'What is in this image?',
    'image',
    ['format' => 'jpeg', 'url' => 'https://example.com/image.jpg']
);

// Audio message
$memory->addUserMessage(
    'Transcribe this audio',
    'audio',
    ['format' => 'mp3', 'url' => 'https://example.com/audio.mp3']
);
```

## Message Metadata

You can attach metadata to any message:

```php
$memory->addUserMessage(
    'Hello!',
    'text',
    [
        'timestamp' => now(),
        'user_id' => 123,
        'custom_data' => ['key' => 'value'],
    ]
);
```

## Message IDs

You can assign unique IDs to messages for tracking:

```php
use Illuminate\Support\Str;

$memory->addUserMessage(
    'Hello!',
    'text',
    [],
    'msg_' . Str::uuid()
);
```

## Parent Context

Memory operations require a parent context to be set. This is handled internally by the memory drivers:

```php
// The memory driver automatically sets the parent context
// based on your conversation or model instance
$messages = $memory->getMessages();
```

## Key-Value Storage

Each memory driver also supports key-value storage operations:

```php
// Store a value
$memory->store('user_preference', ['theme' => 'dark']);

// Check if exists
if ($memory->exists('user_preference')) {
    // Retrieve the value
    $preference = $memory->retrieve('user_preference');
}

// Delete the value
$memory->delete('user_preference');
```

## Media Content Handling

The memory system automatically handles different types of media content:

```php
// Single text content
$memory->addUserMessage('Hello!');

// Mixed content with text and media
$memory->addUserMessage([
    ['type' => 'text', 'text' => 'What is in this image?'],
    [
        'type' => 'image',
        'url' => 'https://example.com/image.jpg',
        'format' => 'jpeg',
    ],
]);
```

## Error Handling

```php
try {
    $messages = $memory->getMessages();
} catch (\RuntimeException $e) {
    // Handle missing parent context
    Log::error('Memory Error', [
        'message' => $e->getMessage(),
    ]);
} catch (\Exception $e) {
    // Handle other errors
    Log::error('Unexpected Error', [
        'message' => $e->getMessage(),
    ]);
}
```

Common exceptions:
- `RuntimeException`: Thrown when parent context is not set
- `InvalidArgumentException`: Thrown when using an unsupported memory driver
- `RuntimeException`: Thrown when a driver class doesn't exist or doesn't implement the interface

## Available Drivers

The package includes three memory drivers:

1. **OpenAI Memory Driver**
   - Uses OpenAI embeddings for memory operations
   - Supports standard message roles (system, user, assistant)
   - Handles text and media content

2. **Gemini Memory Driver**
   - Uses Google Gemini embeddings
   - Supports standard message roles
   - Handles text and media content

3. **Claude Memory Driver**
   - Uses Anthropic Claude embeddings
   - Supports standard message roles
   - Handles text and media content

## Best Practices

1. **Message Management**
   - Use appropriate message types
   - Include relevant metadata
   - Assign unique message IDs for tracking
   - Clear old messages when no longer needed

2. **Error Handling**
   - Always handle potential exceptions
   - Validate message content
   - Check parent context availability

3. **Performance**
   - Use appropriate drivers for your use case
   - Monitor storage usage
   - Implement cleanup strategies

4. **Security**
   - Validate and sanitize message content
   - Handle sensitive data appropriately
   - Use secure storage backends

## Next Steps

1. Learn about [AI Providers](ai-providers)
2. Explore [Streaming](streaming)
3. Read about [Error Handling](../advanced/error-handling)

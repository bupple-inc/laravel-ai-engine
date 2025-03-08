# Memory Interface

The Memory Interface provides methods for managing conversation history and context across different AI providers.

## Basic Usage

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get memory instance with default driver
$memory = BuppleEngine::memory();

// Get memory instance with specific driver
$memory = BuppleEngine::memory('openai'); // or 'gemini', 'claude'
```

## Available Methods

### addMessage()

Add a message to the chat history.

```php
public function addMessage(
    string $role,
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void
```

#### Parameters:
- `role` (string): The role of the message sender ('user', 'assistant', 'system')
- `content` (string): The message content
- `type` (string|null): Message type, defaults to 'text'
- `metadata` (array): Additional metadata for the message
- `messageId` (string|null): Optional unique identifier for the message

#### Example:
```php
$memory->addMessage('user', 'What is Laravel?');
$memory->addMessage(
    role: 'system',
    content: 'You are a Laravel expert',
    type: 'instruction',
    metadata: ['priority' => 'high'],
    messageId: 'sys-001'
);
```

### addUserMessage()

Convenience method to add a user message.

```php
public function addUserMessage(
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void
```

#### Example:
```php
$memory->addUserMessage('How do I use Laravel migrations?');
```

### addAssistantMessage()

Convenience method to add an assistant message.

```php
public function addAssistantMessage(
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void
```

#### Example:
```php
$memory->addAssistantMessage('Here\'s how migrations work in Laravel...');
```

### addSystemMessage()

Convenience method to add a system message.

```php
public function addSystemMessage(
    string $content,
    ?string $type = 'text',
    array $metadata = [],
    ?string $messageId = null
): void
```

#### Example:
```php
$memory->addSystemMessage('You are a helpful Laravel assistant');
```

### getMessages()

Retrieve all messages from the chat history.

```php
public function getMessages(): array
```

#### Returns:
- `array`: Array of messages in chronological order

#### Example:
```php
$messages = $memory->getMessages();

// Messages structure
[
    [
        'role' => 'system',
        'content' => 'You are a helpful assistant',
        'type' => 'text',
        'metadata' => [],
        'message_id' => null
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?',
        'type' => 'text',
        'metadata' => [],
        'message_id' => null
    ],
    // ...
]
```

### clear()

Clear all messages from the chat history.

```php
public function clear(): void
```

#### Example:
```php
$memory->clear();
```

### setParent()

Set the parent context for the memory.

```php
public function setParent(string $class, string|int $id): void
```

#### Parameters:
- `class` (string): The parent class or context type
- `id` (string|int): The identifier for the parent

#### Example:
```php
// Using model class
$memory->setParent(User::class, auth()->id());

// Using custom context
$memory->setParent('conversation', $conversationId);
```

## Storage Drivers

The Memory Interface supports multiple storage drivers:

### OpenAI Memory Driver
```php
$memory = BuppleEngine::memory('openai');
```
- Uses OpenAI embeddings for semantic memory storage
- Optimized for OpenAI chat completions

### Gemini Memory Driver
```php
$memory = BuppleEngine::memory('gemini');
```
- Uses Google Gemini embeddings
- Optimized for Gemini chat completions

### Claude Memory Driver
```php
$memory = BuppleEngine::memory('claude');
```
- Uses Anthropic Claude embeddings
- Optimized for Claude chat completions

## Error Handling

The Memory Interface throws `MemoryException` when there are issues with memory operations:

```php
use Bupple\Engine\Exceptions\MemoryException;

try {
    $memory->setParent(User::class, auth()->id());
    $memory->addMessage('user', 'Hello!');
    $messages = $memory->getMessages();
} catch (MemoryException $e) {
    // Handle memory-related errors
    $errorMessage = $e->getMessage();
    $errorCode = $e->getCode();
}
```

## Best Practices

1. **Parent Context**:
   - Always set parent context before adding messages
   - Use meaningful context identifiers
   - Consider using model classes for type safety

2. **Message Management**:
   - Use appropriate role for each message
   - Include relevant metadata when needed
   - Use message IDs for tracking important messages

3. **Memory Cleanup**:
   - Clear memory when conversation ends
   - Implement periodic cleanup for old conversations
   - Handle memory limits appropriately

4. **Error Handling**:
   - Always implement proper error handling
   - Check for parent context before operations
   - Handle storage failures gracefully

## Examples

### Basic Conversation Flow
```php
$memory = BuppleEngine::memory();
$memory->setParent('conversation', $conversationId);

// Add initial system message
$memory->addSystemMessage('You are a Laravel expert');

// Add user question
$memory->addUserMessage('How do I create a migration?');

// Get conversation history
$messages = $memory->getMessages();

// Send to AI
$response = BuppleEngine::ai()->send($messages);

// Store AI response
$memory->addAssistantMessage($response['content']);
```

### With Metadata and Message IDs
```php
$memory = BuppleEngine::memory();
$memory->setParent(User::class, auth()->id());

$memory->addMessage(
    role: 'user',
    content: 'Help me with Laravel authentication',
    type: 'question',
    metadata: [
        'category' => 'auth',
        'priority' => 'high',
        'source' => 'web'
    ],
    messageId: 'q-' . uniqid()
);
```

### Multiple Contexts
```php
$memory = BuppleEngine::memory();

// User-specific context
$memory->setParent(User::class, $userId);
$userMessages = $memory->getMessages();

// Conversation-specific context
$memory->setParent('conversation', $conversationId);
$conversationMessages = $memory->getMessages();
``` 
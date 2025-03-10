# Memory Interface

The Memory Interface (`MemoryDriverInterface`) defines the contract that all memory drivers must implement for storing and retrieving conversation history.

## Interface Definition

```php
namespace Bupple\Engine\Core\Drivers\Memory\Contracts;

interface MemoryDriverInterface
{
    /**
     * Add a user message to memory.
     *
     * @param string $content Message content
     * @param string|null $type Message type (default: 'text')
     * @param array $metadata Additional metadata
     * @param string|null $messageId Unique message identifier
     * @return void
     */
    public function addUserMessage(
        string $content,
        ?string $type = 'text',
        array $metadata = [],
        ?string $messageId = null
    ): void;

    /**
     * Add an assistant message to memory.
     *
     * @param string $content Message content
     * @param string|null $type Message type (default: 'text')
     * @param array $metadata Additional metadata
     * @param string|null $messageId Unique message identifier
     * @return void
     */
    public function addAssistantMessage(
        string $content,
        ?string $type = 'text',
        array $metadata = [],
        ?string $messageId = null
    ): void;

    /**
     * Add a system message to memory.
     *
     * @param string $content Message content
     * @param string|null $type Message type (default: 'text')
     * @param array $metadata Additional metadata
     * @param string|null $messageId Unique message identifier
     * @return void
     */
    public function addSystemMessage(
        string $content,
        ?string $type = 'text',
        array $metadata = [],
        ?string $messageId = null
    ): void;

    /**
     * Add a message with custom role to memory.
     *
     * @param string $role Message role
     * @param string $content Message content
     * @param string|null $type Message type (default: 'text')
     * @param array $metadata Additional metadata
     * @param string|null $messageId Unique message identifier
     * @return void
     */
    public function addMessage(
        string $role,
        string $content,
        ?string $type = 'text',
        array $metadata = [],
        ?string $messageId = null
    ): void;

    /**
     * Get all messages from memory.
     *
     * @return array Array of messages
     */
    public function getMessages(): array;

    /**
     * Clear all messages from memory.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Set the parent context for memory operations.
     *
     * @param string $class Parent class name
     * @param string|int $id Parent identifier
     * @return void
     */
    public function setParent(string $class, string|int $id): void;
}
```

## Message Format

Messages stored in memory follow this format:

```php
[
    'role' => 'system|user|assistant',
    'content' => 'Message content',
    'type' => 'text|image|audio',
    'metadata' => [
        'user_id' => 123,
        'session_id' => 'abc-xyz',
        'timestamp' => '2024-03-10 12:34:56',
        // Additional metadata
    ],
    'message_id' => 'unique_id',
    'created_at' => '2024-03-10 12:34:56',
    'updated_at' => '2024-03-10 12:34:56'
]
```

## Implementation Example

Here's a basic example of implementing the interface:

```php
use Bupple\Engine\Core\Drivers\Memory\AbstractMemoryDriver;

class CustomMemoryDriver extends AbstractMemoryDriver
{
    protected string $parentClass;
    protected string|int $parentId;

    public function addMessage(
        string $role,
        string $content,
        ?string $type = 'text',
        array $metadata = [],
        ?string $messageId = null
    ): void {
        $message = [
            'parent_class' => $this->parentClass,
            'parent_id' => $this->parentId,
            'role' => $this->formatRole($role),
            'content' => $content,
            'type' => $type,
            'metadata' => $metadata,
            'message_id' => $messageId ?? uniqid('msg_'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->store($message);
    }

    public function getMessages(): array
    {
        return $this->retrieve();
    }

    public function clear(): void
    {
        $this->delete();
    }

    public function setParent(string $class, string|int $id): void
    {
        $this->parentClass = $class;
        $this->parentId = $id;
    }

    protected function formatRole(string $role): string
    {
        return strtolower($role);
    }

    protected function store(array $message): void
    {
        // Implement storage logic
    }

    protected function retrieve(): array
    {
        // Implement retrieval logic
        return [];
    }

    protected function delete(): void
    {
        // Implement deletion logic
    }
}
```

## Exceptions

The interface may throw the following exceptions:

- `MemoryException`: Base exception for all memory-related errors
- `ConnectionException`: When connection to storage fails
- `StorageException`: When storage operations fail
- `ValidationException`: When input validation fails

## Usage Example

```php
use Bupple\Engine\Facades\Engine;

// Set parent context
Engine::memory()->setParent('ChatSession', 123);

// Add messages
Engine::memory()->addUserMessage('Hello!');
Engine::memory()->addAssistantMessage('Hi! How can I help?');
Engine::memory()->addSystemMessage('You are a helpful assistant.');

// Add message with metadata
Engine::memory()->addMessage('user', 'Hello!', 'text', [
    'user_id' => 456,
    'session_id' => 'abc-xyz'
]);

// Get all messages
$messages = Engine::memory()->getMessages();

// Clear memory
Engine::memory()->clear();
``` 
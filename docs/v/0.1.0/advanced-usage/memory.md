# Advanced Memory Usage

This guide covers advanced features and techniques for using the Bupple AI Engine memory management system.

## Advanced Message Management

### Message with Metadata

```php
use Bupple\Engine\Facades\Engine;

Engine::memory()->addMessage('user', 'Hello!', 'text', [
    'user_id' => 123,
    'session_id' => 'abc-xyz',
    'timestamp' => now(),
    'client_info' => [
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ],
]);
```

### Media Content Storage

```php
// Store image message
Engine::memory()->addMessage('user', $imageUrl, 'image', [
    'mime_type' => 'image/jpeg',
    'size' => $imageSize,
    'dimensions' => [
        'width' => 800,
        'height' => 600,
    ],
]);

// Store audio message
Engine::memory()->addMessage('user', $audioUrl, 'audio', [
    'format' => 'mp3',
    'duration' => 120,
    'transcript' => 'Audio transcript here',
]);
```

## Advanced Context Management

### Multiple Contexts

```php
// User context
Engine::memory()->setParent(User::class, $userId);
$userMessages = Engine::memory()->getMessages();

// Chat context
Engine::memory()->setParent('ChatSession', $sessionId);
$chatMessages = Engine::memory()->getMessages();

// Thread context
Engine::memory()->setParent('Thread', $threadId);
$threadMessages = Engine::memory()->getMessages();
```

### Context Inheritance

```php
class ChatMemory
{
    protected $memory;
    protected $contexts = [];

    public function __construct()
    {
        $this->memory = Engine::memory();
    }

    public function pushContext(string $class, string|int $id): void
    {
        $this->contexts[] = [$class, $id];
        $this->memory->setParent($class, $id);
    }

    public function popContext(): void
    {
        array_pop($this->contexts);
        if ($context = end($this->contexts)) {
            $this->memory->setParent($context[0], $context[1]);
        }
    }

    public function getAllMessages(): array
    {
        $messages = [];
        foreach ($this->contexts as $context) {
            $this->memory->setParent($context[0], $context[1]);
            $messages = array_merge($messages, $this->memory->getMessages());
        }
        return $messages;
    }
}
```

## Custom Memory Drivers

### Creating a Custom Driver

```php
use Bupple\Engine\Core\Drivers\Memory\AbstractMemoryDriver;
use Bupple\Engine\Core\Models\Memory;

class CustomMemoryDriver extends AbstractMemoryDriver
{
    protected function formatRole(string $role): string
    {
        return strtolower($role);
    }

    protected function formatMessage(Memory $message): array
    {
        return [
            'role' => $message->role,
            'content' => $this->formatContent($message),
            'metadata' => $message->metadata,
        ];
    }

    protected function formatContent($message): string|array
    {
        if ($message->type === 'text') {
            return $message->content;
        }

        return match ($message->type) {
            'image' => $this->formatImageContent($message),
            'audio' => $this->formatAudioContent($message),
            default => $message->content,
        };
    }

    protected function getDriverName(): string
    {
        return 'custom';
    }
}
```

### Registering Custom Driver

```php
use Bupple\Engine\Core\Drivers\Memory\MemoryManager;

class CustomMemoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->extend(MemoryManager::class, function ($manager) {
            $manager->extend('custom', function ($config) {
                return new CustomMemoryDriver($config);
            });
            return $manager;
        });
    }
}
```

## Advanced Querying

### Message Filtering

```php
use Bupple\Engine\Core\Models\Memory;

// Get messages by type
$imageMessages = Memory::where('type', 'image')->get();

// Get messages by role
$userMessages = Memory::where('role', 'user')->get();

// Get messages with specific metadata
$messages = Memory::whereJsonContains('metadata->tags', ['important'])->get();

// Get messages within time range
$recentMessages = Memory::whereBetween('created_at', [
    now()->subHours(24),
    now()
])->get();
```

### Message Aggregation

```php
// Count messages by role
$messageCounts = Memory::groupBy('role')
    ->selectRaw('role, count(*) as count')
    ->get();

// Get average message length
$avgLength = Memory::where('type', 'text')
    ->selectRaw('AVG(LENGTH(content)) as avg_length')
    ->first();

// Get most active users
$activeUsers = Memory::where('role', 'user')
    ->groupBy('parent_id')
    ->selectRaw('parent_id, count(*) as message_count')
    ->orderByDesc('message_count')
    ->limit(10)
    ->get();
```

## Performance Optimization

### Batch Operations

```php
// Batch insert messages
public function batchAddMessages(array $messages)
{
    $formattedMessages = array_map(function ($message) {
        return [
            'parent_class' => $this->parentClass,
            'parent_id' => $this->parentId,
            'role' => $this->formatRole($message['role']),
            'content' => $message['content'],
            'type' => $message['type'] ?? 'text',
            'metadata' => $message['metadata'] ?? [],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }, $messages);

    Memory::insert($formattedMessages);
}

// Batch delete messages
public function batchDeleteMessages(array $conditions)
{
    Memory::where($conditions)->delete();
}
```

### Caching

```php
use Illuminate\Support\Facades\Cache;

public function getCachedMessages(): array
{
    $cacheKey = "memory:{$this->parentClass}:{$this->parentId}";
    
    return Cache::remember($cacheKey, now()->addMinutes(5), function () {
        return $this->getMessages();
    });
}

public function invalidateCache(): void
{
    $cacheKey = "memory:{$this->parentClass}:{$this->parentId}";
    Cache::forget($cacheKey);
}
```

## Next Steps

For more advanced topics, check out:
- [Advanced Engine Usage](./engine.md)
- [Advanced SSE Usage](./sse.md)
- [Error Handling](./error-handling.md) 
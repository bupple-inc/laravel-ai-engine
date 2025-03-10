# API Reference Overview

This section provides detailed API documentation for the Bupple AI Engine package.

## Core Components

### BuppleEngine

The main class that provides access to all engine functionality.

```php
use Bupple\Engine\BuppleEngine;

class BuppleEngine
{
    public function memory(): MemoryManager;
    public function engine(?string $name = null): EngineDriverInterface;
    public function sse(): SseStreamDriver;
    public function jsonParserHelper(): JsonParserHelper;
    public function driver(?string $driver = null): MemoryDriverInterface;
    public function config(?string $key = null, mixed $default = null): mixed;
}
```

### Memory Manager

Manages memory storage and retrieval across different drivers.

```php
use Bupple\Engine\Core\Drivers\Memory\MemoryManager;

class MemoryManager
{
    public function driver(?string $name = null): MemoryDriverInterface;
    public function getDefaultDriver(): string;
    public function setDefaultDriver(string $name): void;
    public function getConfig(?string $name = null): array;
}
```

### SSE Stream Driver

Handles Server-Sent Events streaming.

```php
use Bupple\Engine\Core\Drivers\Stream\SseStreamDriver;

class SseStreamDriver
{
    public function send(mixed $data, ?string $event = null): void;
    public function sendError(mixed $data): void;
    public function end(): void;
}
```

## Interfaces

### Engine Driver Interface

Interface for AI engine drivers.

```php
use Bupple\Engine\Core\Drivers\Engine\Contracts\EngineDriverInterface;

interface EngineDriverInterface
{
    public function send(array $messages): array;
    public function stream(array $messages): \Generator;
    public function getConfig(): array;
}
```

### Memory Driver Interface

Interface for memory storage drivers.

```php
use Bupple\Engine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;

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

## Engine Drivers

### OpenAI Driver

```php
use Bupple\Engine\Core\Drivers\Engine\OpenAI\OpenAIDriver;

class OpenAIDriver extends AbstractEngineDriver
{
    public function send(array $messages): array;
    public function stream(array $messages): \Generator;
    public function getConfig(): array;
}
```

### Gemini Driver

```php
use Bupple\Engine\Core\Drivers\Engine\Gemini\GeminiDriver;

class GeminiDriver extends AbstractEngineDriver
{
    public function send(array $messages): array;
    public function stream(array $messages): \Generator;
    public function getConfig(): array;
}
```

### Claude Driver

```php
use Bupple\Engine\Core\Drivers\Engine\Claude\ClaudeDriver;

class ClaudeDriver extends AbstractEngineDriver
{
    public function send(array $messages): array;
    public function stream(array $messages): \Generator;
    public function getConfig(): array;
}
```

## Memory Drivers

### File Memory Driver

```php
use Bupple\Engine\Core\Drivers\Memory\FileMemoryDriver;

class FileMemoryDriver extends AbstractMemoryDriver
{
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function getMessages(): array;
    public function clear(): void;
}
```

### Database Memory Driver

```php
use Bupple\Engine\Core\Drivers\Memory\DatabaseMemoryDriver;

class DatabaseMemoryDriver extends AbstractMemoryDriver
{
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function getMessages(): array;
    public function clear(): void;
}
```

### Redis Memory Driver

```php
use Bupple\Engine\Core\Drivers\Memory\RedisMemoryDriver;

class RedisMemoryDriver extends AbstractMemoryDriver
{
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function getMessages(): array;
    public function clear(): void;
}
```

## Helpers

### JSON Parser Helper

```php
use Bupple\Engine\Core\Helpers\JsonParserHelper;

class JsonParserHelper
{
    public function parse(string $json): mixed;
}
```

## Next Steps

For detailed documentation on each component, see:
- [Engine Interface](./engine/interface.md)
- [Engine Provider](./engine/provider.md)
- [Memory Interface](./memory/interface.md)
- [Memory Provider](./memory/provider.md) 
# Chat Driver Interface

The Chat Driver Interface provides a standardized way to interact with different AI providers in the Bupple Laravel AI Engine.

## Interface Definition

```php
interface ChatDriverInterface
{
    public function send(array $messages): array;
    public function stream(array $messages): \Generator;
    public function getConfig(): array;
}
```

## Method Descriptions

### send()

Sends messages to the AI provider and returns a response.

```php
public function send(array $messages): array;
```

Parameters:
- `$messages`: Array of messages to send to the AI provider
  ```php
  [
      [
          'role' => 'system|user|assistant',
          'content' => 'Message content',
          'type' => 'text|image|audio', // optional
          'metadata' => [], // optional
      ],
      // ...
  ]
  ```

Returns:
- `array`: The AI provider's response
  ```php
  [
      'role' => 'assistant',
      'content' => 'Response content',
      'model' => 'Model name (e.g., gpt-4)',
      'usage' => [
          // Provider-specific usage data
      ],
  ]
  ```

### stream()

Creates a streaming response for real-time AI interactions.

```php
public function stream(array $messages): \Generator;
```

Parameters:
- `$messages`: Array of messages to stream to the AI provider

Returns:
- `\Generator`: Yields response chunks
  ```php
  [
      'role' => 'assistant',
      'content' => 'Partial response content',
      'done' => false,
  ]
  ```

### getConfig()

Gets the current AI provider configuration.

```php
public function getConfig(): array;
```

Returns:
- `array`: The current configuration

## Basic Usage

### Simple Chat Completion

```php
use Bupple\Engine\Facades\BuppleEngine;

// Using default provider
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Using specific provider
$response = BuppleEngine::ai('openai')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

### With System Messages

```php
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

### Streaming Response

```php
$stream = BuppleEngine::ai()->stream([
    ['role' => 'user', 'content' => 'Write a story...']
]);

foreach ($stream as $chunk) {
    echo $chunk['content'];
}
```

## Error Handling

```php
try {
    $response = BuppleEngine::ai()->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (\RuntimeException $e) {
    // Handle API errors
    Log::error('AI Error', [
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
} catch (\Exception $e) {
    // Handle other errors
    Log::error('Unexpected Error', [
        'message' => $e->getMessage(),
    ]);
}
```

## Best Practices

1. **Message Format**
   - Use appropriate role values
   - Include system messages for context
   - Keep messages concise and clear

2. **Error Handling**
   - Implement proper error handling
   - Log errors appropriately
   - Handle timeouts and connection issues

3. **Performance**
   - Use streaming for long responses
   - Monitor API usage
   - Handle rate limits appropriately

## Next Steps

1. Learn about [Memory Interface](memory-interface)
2. Explore [SSE Interface](sse-interface)
3. Read about [Configuration](configuration)

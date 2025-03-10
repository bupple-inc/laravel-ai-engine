# Advanced Engine Usage

This guide covers advanced features and techniques for using the Bupple AI Engine capabilities.

## Custom Model Parameters

### OpenAI Advanced Configuration

```php
use Bupple\Engine\Facades\Engine;

$response = Engine::engine('openai')->send([
    ['role' => 'user', 'content' => 'Generate a creative story']
], [
    'model' => 'gpt-4',
    'temperature' => 0.9,
    'max_tokens' => 2000,
    'presence_penalty' => 0.6,
    'frequency_penalty' => 0.6,
]);
```

### Gemini Advanced Configuration

```php
$response = Engine::engine('gemini')->send([
    ['role' => 'user', 'content' => 'Explain quantum computing']
], [
    'model' => 'gemini-pro',
    'temperature' => 0.3,
    'max_tokens' => 1500,
    'top_p' => 0.8,
]);
```

### Claude Advanced Configuration

```php
$response = Engine::engine('claude')->send([
    ['role' => 'user', 'content' => 'Analyze this code']
], [
    'model' => 'claude-3-opus-20240229',
    'temperature' => 0.2,
    'max_tokens' => 3000,
]);
```

## Media Content Handling

### Image Processing

```php
// OpenAI Vision
$response = Engine::engine('openai')->send([
    [
        'role' => 'user',
        'content' => [
            ['type' => 'text', 'text' => 'What\'s in this image?'],
            [
                'type' => 'image_url',
                'image_url' => [
                    'url' => 'https://example.com/image.jpg',
                    'detail' => 'high'
                ]
            ]
        ]
    ]
]);

// Gemini Vision
$response = Engine::engine('gemini')->send([
    [
        'role' => 'user',
        'content' => [
            ['type' => 'text', 'text' => 'Describe this image'],
            [
                'type' => 'inline_data',
                'mime_type' => 'image/jpeg',
                'data' => base64_encode($imageData)
            ]
        ]
    ]
]);
```

### Audio Processing

```php
$response = Engine::engine('openai')->send([
    [
        'role' => 'user',
        'content' => [
            ['type' => 'text', 'text' => 'Transcribe this audio'],
            [
                'type' => 'audio',
                'audio' => [
                    'url' => 'https://example.com/audio.mp3',
                    'format' => 'mp3'
                ]
            ]
        ]
    ]
]);
```

## Advanced Error Handling

```php
use Bupple\Engine\Core\Drivers\Engine\Exceptions\EngineException;
use Bupple\Engine\Core\Drivers\Engine\Exceptions\RateLimitException;
use Bupple\Engine\Core\Drivers\Engine\Exceptions\AuthenticationException;

try {
    $response = Engine::engine()->send($messages);
} catch (RateLimitException $e) {
    // Handle rate limiting
    sleep(5);
    retry($e->getRetryAfter());
} catch (AuthenticationException $e) {
    // Handle authentication errors
    refreshApiKey();
} catch (EngineException $e) {
    // Handle other engine-specific errors
    logError($e);
    fallbackToAlternativeEngine();
} catch (\Exception $e) {
    // Handle general errors
    reportError($e);
}
```

## Custom Engine Implementation

Create your own engine driver by extending the abstract class:

```php
use Bupple\Engine\Core\Drivers\Engine\AbstractEngineDriver;

class CustomEngineDriver extends AbstractEngineDriver
{
    protected function getBaseUri(): string
    {
        return 'https://api.custom-ai.com/v1/';
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->config['api_key'],
            'Content-Type' => 'application/json',
        ];
    }

    protected function formatMessages(array $messages): array
    {
        return array_map(function ($message) {
            return [
                'role' => $this->mapRole($message['role']),
                'content' => $message['content'],
            ];
        }, $messages);
    }

    protected function formatOptions(array $options): array
    {
        return [
            'model' => $options['model'] ?? 'default-model',
            'temperature' => (float) ($options['temperature'] ?? 0.7),
            'max_tokens' => (int) ($options['max_tokens'] ?? 1000),
        ];
    }

    protected function mapRole(string $role): string
    {
        return match ($role) {
            'system' => 'system',
            'assistant' => 'bot',
            'user' => 'human',
            default => 'human',
        };
    }
}
```

## Advanced Configuration Management

```php
// Dynamic configuration updates
Engine::engine()->config([
    'model' => 'gpt-4-turbo',
    'temperature' => calculateDynamicTemperature(),
    'max_tokens' => determineContextLength($messages),
]);

// Environment-specific configuration
if (app()->environment('production')) {
    Engine::engine()->config([
        'model' => 'gpt-4',
        'temperature' => 0.3,
    ]);
} else {
    Engine::engine()->config([
        'model' => 'gpt-3.5-turbo',
        'temperature' => 0.7,
    ]);
}
```

## Performance Optimization

```php
// Batch processing
$responses = collect($messages)->chunk(5)->map(function ($chunk) {
    return Engine::engine()->send($chunk->toArray());
});
```

## Next Steps

For more advanced topics, check out:
- [Advanced Memory Usage](./memory.md)
- [Advanced SSE Usage](./sse.md)
- [Error Handling](./error-handling.md) 
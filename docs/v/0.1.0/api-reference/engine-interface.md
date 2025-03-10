# Engine Interface

The Engine Interface (`EngineDriverInterface`) defines the contract that all AI engine drivers must implement.

## Interface Definition

```php
namespace Bupple\Engine\Core\Drivers\Engine\Contracts;

interface EngineDriverInterface
{
    /**
     * Send messages to the AI engine and get a response.
     *
     * @param array $messages Array of message objects
     * @return array Response from the AI engine
     * @throws EngineException
     */
    public function send(array $messages): array;

    /**
     * Stream messages to the AI engine and get responses as a generator.
     *
     * @param array $messages Array of message objects
     * @return \Generator Stream of responses
     * @throws EngineException
     */
    public function stream(array $messages): \Generator;

    /**
     * Get the engine configuration.
     *
     * @return array Configuration array
     */
    public function getConfig(): array;
}
```

## Message Format

Messages passed to `send()` and `stream()` should follow this format:

```php
[
    [
        'role' => 'system|user|assistant',
        'content' => 'Message content',
        'type' => 'text|image|audio', // Optional, defaults to 'text'
        'metadata' => [], // Optional additional data
    ],
    // ... more messages
]
```

## Response Format

The `send()` method returns a response in this format:

```php
[
    'role' => 'assistant',
    'content' => 'Response content',
    'model' => 'Model name (e.g., gpt-4)',
    'usage' => [
        'prompt_tokens' => 123,
        'completion_tokens' => 456,
        'total_tokens' => 579
    ]
]
```

## Stream Format

The `stream()` method yields chunks in this format:

```php
[
    'role' => 'assistant',
    'content' => 'Partial response content',
    'model' => 'Model name',
    'finish_reason' => null|'stop|length|content_filter'
]
```

## Configuration Format

The `getConfig()` method returns configuration in this format:

```php
[
    'api_key' => 'API key',
    'model' => 'Model name',
    'temperature' => 0.7,
    'max_tokens' => 1000,
    // Additional engine-specific configuration
]
```

## Exceptions

The interface may throw the following exceptions:

- `EngineException`: Base exception for all engine-related errors
- `RateLimitException`: When API rate limits are exceeded
- `AuthenticationException`: When API authentication fails
- `ValidationException`: When input validation fails
- `ConnectionException`: When connection to the API fails

## Implementation Example

Here's a basic example of implementing the interface:

```php
use Bupple\Engine\Core\Drivers\Engine\AbstractEngineDriver;

class CustomEngineDriver extends AbstractEngineDriver
{
    public function send(array $messages): array
    {
        // Validate messages
        $this->validateMessages($messages);

        // Format messages for the API
        $formatted = $this->formatMessages($messages);

        // Send request to API
        $response = $this->makeRequest($formatted);

        // Format and return response
        return $this->formatResponse($response);
    }

    public function stream(array $messages): \Generator
    {
        // Similar to send(), but yield chunks
        foreach ($this->streamRequest($messages) as $chunk) {
            yield $this->formatChunk($chunk);
        }
    }

    public function getConfig(): array
    {
        return [
            'api_key' => $this->config['api_key'],
            'model' => $this->config['model'],
            'temperature' => $this->config['temperature'] ?? 0.7,
            'max_tokens' => $this->config['max_tokens'] ?? 1000,
        ];
    }
}
``` 
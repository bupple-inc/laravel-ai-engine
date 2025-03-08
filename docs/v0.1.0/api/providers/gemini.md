# Gemini Provider

The Gemini provider integrates with Google's Gemini models through their official API.

## Configuration

```php
use Bupple\Engine\Facades\BuppleEngine;

// Initialize with Gemini provider
$ai = BuppleEngine::ai('gemini');
```

## Available Models

- `gemini-pro`
- `gemini-pro-vision` (coming soon)

## Methods

### send()

Send a message to Gemini's API:

```php
$response = $ai->send([
    ['role' => 'system', 'content' => 'You are a helpful assistant'],
    ['role' => 'user', 'content' => 'Hello!']
]);

// Response structure
[
    'content' => string,      // The response content
    'model' => string,        // The model used (e.g., 'gemini-pro')
    'provider' => 'gemini',   // Provider identifier
    'usage' => [
        'prompt_tokens' => int,
        'completion_tokens' => int,
        'total_tokens' => int
    ]
]
```

### stream()

Stream responses from Gemini's API:

```php
$stream = $ai->stream([
    ['role' => 'user', 'content' => 'Write a story']
]);

foreach ($stream as $chunk) {
    // Chunk structure
    [
        'content' => string,      // The chunk content
        'model' => string,        // The model used
        'provider' => 'gemini',   // Provider identifier
        'finish_reason' => string // Reason for finishing (null if not finished)
    ]
}
```

### withModel()

Override the default model:

```php
$response = $ai->withModel('gemini-pro')->send($messages);
```

### withTemperature()

Set the temperature (0.0 to 1.0):

```php
$response = $ai->withTemperature(0.7)->send($messages);
```

### withMaxTokens()

Set the maximum tokens for the response:

```php
$response = $ai->withMaxTokens(1000)->send($messages);
```

### withSystemMessage()

Set a system message for the conversation:

```php
$response = $ai->withSystemMessage('You are a helpful assistant')
    ->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
```

## Error Handling

Gemini-specific error handling:

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = $ai->send($messages);
} catch (AiProviderException $e) {
    if ($e->getProvider() === 'gemini') {
        // Handle Gemini-specific errors
        match ($e->getCode()) {
            401 => 'Invalid API key',
            429 => 'Rate limit exceeded',
            500 => 'Gemini server error',
            default => 'Unknown error'
        };
    }
}
```

## Best Practices

1. **Model Selection**:
   - Use `gemini-pro` for general text generation and conversations
   - Future support for `gemini-pro-vision` for image-related tasks

2. **Token Management**:
   - Monitor token usage through the response's `usage` field
   - Set appropriate `max_tokens` based on expected response length
   - Consider implementing token counting on your end

3. **Temperature Settings**:
   - Use lower temperatures (0.0-0.3) for factual responses
   - Use higher temperatures (0.7-1.0) for creative content
   - Default is 0.7 for balanced responses

4. **Rate Limiting**:
   - Implement exponential backoff for rate limit errors
   - Consider using a queue for high-volume requests
   - Monitor rate limit headers in responses

## Examples

### Basic Conversation

```php
$response = BuppleEngine::ai('gemini')
    ->withModel('gemini-pro')
    ->withTemperature(0.7)
    ->send([
        ['role' => 'system', 'content' => 'You are a helpful assistant'],
        ['role' => 'user', 'content' => 'What is Laravel?']
    ]);

echo $response['content'];
```

### Streaming with Error Handling

```php
try {
    $stream = BuppleEngine::ai('gemini')
        ->withModel('gemini-pro')
        ->stream([
            ['role' => 'user', 'content' => 'Write a story']
        ]);

    foreach ($stream as $chunk) {
        echo $chunk['content'];
        flush();
    }
} catch (AiProviderException $e) {
    // Handle Gemini streaming errors
    echo "Error: " . $e->getMessage();
}
```

### Function Chaining

```php
$response = BuppleEngine::ai('gemini')
    ->withModel('gemini-pro')
    ->withTemperature(0.3)
    ->withMaxTokens(500)
    ->withSystemMessage('You are a Laravel expert')
    ->send([
        ['role' => 'user', 'content' => 'Explain dependency injection']
    ]);
``` 
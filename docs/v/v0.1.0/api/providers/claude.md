# Claude Provider

The Claude provider integrates with Anthropic's Claude models through their official API.

## Configuration

```php
use Bupple\Engine\Facades\BuppleEngine;

// Initialize with Claude provider
$ai = BuppleEngine::ai('claude');
```

## Available Models

- `claude-3-opus-20240229`
- `claude-3-sonnet-20240229`
- `claude-3-haiku-20240229`

## Methods

### send()

Send a message to Claude's API:

```php
$response = $ai->send([
    ['role' => 'system', 'content' => 'You are a helpful assistant'],
    ['role' => 'user', 'content' => 'Hello!']
]);

// Response structure
[
    'content' => string,      // The response content
    'model' => string,        // The model used (e.g., 'claude-3-opus-20240229')
    'provider' => 'claude',   // Provider identifier
    'usage' => [
        'prompt_tokens' => int,
        'completion_tokens' => int,
        'total_tokens' => int
    ]
]
```

### stream()

Stream responses from Claude's API:

```php
$stream = $ai->stream([
    ['role' => 'user', 'content' => 'Write a story']
]);

foreach ($stream as $chunk) {
    // Chunk structure
    [
        'content' => string,      // The chunk content
        'model' => string,        // The model used
        'provider' => 'claude',   // Provider identifier
        'finish_reason' => string // Reason for finishing (null if not finished)
    ]
}
```

### withModel()

Override the default model:

```php
$response = $ai->withModel('claude-3-opus-20240229')->send($messages);
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

Claude-specific error handling:

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = $ai->send($messages);
} catch (AiProviderException $e) {
    if ($e->getProvider() === 'claude') {
        // Handle Claude-specific errors
        match ($e->getCode()) {
            401 => 'Invalid API key',
            429 => 'Rate limit exceeded',
            500 => 'Claude server error',
            default => 'Unknown error'
        };
    }
}
```

## Best Practices

1. **Model Selection**:
   - Use `claude-3-opus-20240229` for complex tasks requiring high accuracy
   - Use `claude-3-sonnet-20240229` for balanced performance and cost
   - Use `claude-3-haiku-20240229` for simpler tasks or when speed is priority

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
$response = BuppleEngine::ai('claude')
    ->withModel('claude-3-opus-20240229')
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
    $stream = BuppleEngine::ai('claude')
        ->withModel('claude-3-opus-20240229')
        ->stream([
            ['role' => 'user', 'content' => 'Write a story']
        ]);

    foreach ($stream as $chunk) {
        echo $chunk['content'];
        flush();
    }
} catch (AiProviderException $e) {
    // Handle Claude streaming errors
    echo "Error: " . $e->getMessage();
}
```

### Function Chaining

```php
$response = BuppleEngine::ai('claude')
    ->withModel('claude-3-opus-20240229')
    ->withTemperature(0.3)
    ->withMaxTokens(500)
    ->withSystemMessage('You are a Laravel expert')
    ->send([
        ['role' => 'user', 'content' => 'Explain dependency injection']
    ]);
``` 
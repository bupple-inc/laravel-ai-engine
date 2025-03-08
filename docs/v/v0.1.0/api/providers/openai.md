# OpenAI Provider

The OpenAI provider integrates with OpenAI's GPT models through their official API.

## Configuration

```php
use Bupple\Engine\Facades\BuppleEngine;

// Initialize with OpenAI provider
$ai = BuppleEngine::ai('openai');

// Or use if it's the default provider
$ai = BuppleEngine::ai();
```

## Available Models

- `gpt-4`
- `gpt-4-turbo-preview`
- `gpt-3.5-turbo`

## Methods

### send()

Send a message to OpenAI's API:

```php
$response = $ai->send([
    ['role' => 'system', 'content' => 'You are a helpful assistant'],
    ['role' => 'user', 'content' => 'Hello!']
]);

// Response structure
[
    'content' => string,      // The response content
    'model' => string,        // The model used (e.g., 'gpt-4')
    'provider' => 'openai',   // Provider identifier
    'usage' => [
        'prompt_tokens' => int,
        'completion_tokens' => int,
        'total_tokens' => int
    ]
]
```

### stream()

Stream responses from OpenAI's API:

```php
$stream = $ai->stream([
    ['role' => 'user', 'content' => 'Write a story']
]);

foreach ($stream as $chunk) {
    // Chunk structure
    [
        'content' => string,      // The chunk content
        'model' => string,        // The model used
        'provider' => 'openai',   // Provider identifier
        'finish_reason' => string // Reason for finishing (null if not finished)
    ]
}
```

### withModel()

Override the default model:

```php
$response = $ai->withModel('gpt-4')->send($messages);
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

OpenAI-specific error handling:

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = $ai->send($messages);
} catch (AiProviderException $e) {
    if ($e->getProvider() === 'openai') {
        // Handle OpenAI-specific errors
        match ($e->getCode()) {
            401 => 'Invalid API key',
            429 => 'Rate limit exceeded',
            500 => 'OpenAI server error',
            default => 'Unknown error'
        };
    }
}
```

## Best Practices

1. **Model Selection**:
   - Use `gpt-4` for complex tasks requiring high accuracy
   - Use `gpt-3.5-turbo` for simpler tasks or when cost is a concern

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
$response = BuppleEngine::ai('openai')
    ->withModel('gpt-4')
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
    $stream = BuppleEngine::ai('openai')
        ->withModel('gpt-4')
        ->stream([
            ['role' => 'user', 'content' => 'Write a story']
        ]);

    foreach ($stream as $chunk) {
        echo $chunk['content'];
        flush();
    }
} catch (AiProviderException $e) {
    // Handle OpenAI streaming errors
    echo "Error: " . $e->getMessage();
}
```

### Function Chaining

```php
$response = BuppleEngine::ai('openai')
    ->withModel('gpt-4')
    ->withTemperature(0.3)
    ->withMaxTokens(500)
    ->withSystemMessage('You are a Laravel expert')
    ->send([
        ['role' => 'user', 'content' => 'Explain dependency injection']
    ]);
``` 
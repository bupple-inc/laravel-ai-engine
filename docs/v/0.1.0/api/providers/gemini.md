# Gemini Provider

The Gemini provider integrates Google's Gemini AI models into the Bupple Laravel AI Engine.

## Configuration

```php
// config/bupple-engine.php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-pro'),
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
    'project_id' => env('GEMINI_PROJECT_ID', null),
],
```

### Environment Variables

```env
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-pro
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1000
GEMINI_PROJECT_ID=optional-project-id
```

## Basic Usage

### Chat Completion

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get Gemini driver
$gemini = BuppleEngine::ai('gemini');

// Simple completion
$response = $gemini->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// With system message
$response = $gemini->send([
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

### Streaming

```php
// Get streaming response
$stream = $gemini->stream([
    ['role' => 'user', 'content' => 'Write a story...']
]);

foreach ($stream as $chunk) {
    echo $chunk['content'];
}
```

## Response Format

### Chat Completion Response

```php
[
    'role' => 'assistant',
    'content' => 'The response text',
    'model' => 'gemini-pro',
    'usage' => [
        'prompt_tokens' => 10,
        'completion_tokens' => 20,
        'total_tokens' => 30,
    ],
]
```

### Streaming Response Chunk

```php
[
    'role' => 'assistant',
    'content' => 'Partial response text',
    'done' => false,
]
```

## Memory Integration

### Using Gemini Memory Driver

```php
// Get Gemini memory driver
$memory = BuppleEngine::memory()->driver('gemini');

// Add messages
$memory->addUserMessage('What is Laravel?');
$memory->addAssistantMessage('Laravel is a web application framework...');
$memory->addSystemMessage('You are a helpful assistant.');

// Get messages
$messages = $memory->getMessages();

// Use in chat completion
$response = $gemini->send($messages);

// Clear conversation history
$memory->clear();
```

## Error Handling

```php
try {
    $response = $gemini->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (\RuntimeException $e) {
    // Handle API errors
    Log::error('Gemini Error', [
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

1. **Error Handling**
   - Always wrap API calls in try-catch blocks
   - Log errors appropriately
   - Implement proper error recovery

2. **Message Management**
   - Use appropriate message roles (system, user, assistant)
   - Keep messages concise and clear
   - Clear conversation history when appropriate

3. **Configuration**
   - Store API keys securely in environment variables
   - Use appropriate model for your use case
   - Monitor API usage and costs

4. **Performance**
   - Use streaming for long responses
   - Implement proper error handling
   - Monitor response times

## Next Steps

1. Learn about [Memory Management](../../guide/core/memory-management)
2. Explore [Streaming](../../guide/core/streaming)
3. Read about [Error Handling](../../guide/advanced/error-handling)

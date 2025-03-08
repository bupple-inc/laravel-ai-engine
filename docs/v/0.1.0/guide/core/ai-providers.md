# AI Providers

The Bupple Laravel AI Engine supports multiple AI providers, each with its own unique capabilities and characteristics. This guide explains how to use each provider effectively.

## Supported Providers

Currently, the package supports three major AI providers:
1. OpenAI (GPT-4, GPT-3.5)
2. Google Gemini
3. Anthropic Claude

## Common Interface

All providers implement a common interface, making it easy to switch between them:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Using the default provider
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Using a specific provider
$response = BuppleEngine::ai('openai')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

## OpenAI Integration

OpenAI is the default provider and supports GPT-4 and GPT-3.5 models.

### Configuration

```php
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'model' => env('OPENAI_MODEL', 'gpt-4'),
    'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
    'organization_id' => env('OPENAI_ORGANIZATION_ID', null),
],
```

### Basic Usage

```php
// Using OpenAI
$openai = BuppleEngine::ai('openai');

// Simple completion
$response = $openai->send([
    ['role' => 'user', 'content' => 'What is Laravel?']
]);

// With system message
$response = $openai->send([
    [
        'role' => 'system',
        'content' => 'You are a Laravel expert providing concise answers.'
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?'
    ]
]);

// Streaming response
foreach ($openai->stream([
    ['role' => 'user', 'content' => 'Write a long story...']
]) as $chunk) {
    echo $chunk['content'];
}
```

## Google Gemini Integration

Google's Gemini models offer strong performance and competitive pricing.

### Configuration

```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-pro'),
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
    'project_id' => env('GEMINI_PROJECT_ID', null),
],
```

### Basic Usage

```php
// Using Gemini
$gemini = BuppleEngine::ai('gemini');

// Simple completion
$response = $gemini->send([
    ['role' => 'user', 'content' => 'What is Laravel?']
]);

// With context
$response = $gemini->send([
    [
        'role' => 'user',
        'content' => 'Remember that you are a Laravel expert.',
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?'
    ]
]);

// Streaming response
foreach ($gemini->stream([
    ['role' => 'user', 'content' => 'Write a long story...']
]) as $chunk) {
    echo $chunk['content'];
}
```

## Anthropic Claude Integration

Claude offers advanced capabilities and strong performance on complex tasks.

### Configuration

```php
'claude' => [
    'api_key' => env('CLAUDE_API_KEY'),
    'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
    'temperature' => env('CLAUDE_TEMPERATURE', 0.7),
    'max_tokens' => env('CLAUDE_MAX_TOKENS', 1000),
],
```

### Basic Usage

```php
// Using Claude
$claude = BuppleEngine::ai('claude');

// Simple completion
$response = $claude->send([
    ['role' => 'user', 'content' => 'What is Laravel?']
]);

// With system message
$response = $claude->send([
    [
        'role' => 'system',
        'content' => 'You are a Laravel expert providing concise answers.'
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?'
    ]
]);

// Streaming response
foreach ($claude->stream([
    ['role' => 'user', 'content' => 'Write a long story...']
]) as $chunk) {
    echo $chunk['content'];
}
```

## Response Format

All providers return responses in a standardized format:

```php
[
    'role' => 'assistant',
    'content' => 'The response text',
    'model' => 'The model used (e.g., gpt-4, gemini-pro)',
    'usage' => [
        // Provider-specific usage data
    ],
]
```

## Streaming Responses

All providers support streaming responses through Server-Sent Events (SSE):

```php
// In your controller
return response()->stream(function () {
    $stream = BuppleEngine::ai()->stream([
        ['role' => 'user', 'content' => 'Write a story...']
    ]);

    foreach ($stream as $chunk) {
        echo "data: " . json_encode(['content' => $chunk['content']]) . "\n\n";
        ob_flush();
        flush();
    }
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]);
```

## Error Handling

Each provider may throw different types of errors. The package normalizes these into standard exceptions:

```php
try {
    $response = BuppleEngine::ai()->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (\RuntimeException $e) {
    // Handle API errors
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
} catch (\InvalidArgumentException $e) {
    // Handle invalid input
} catch (\Exception $e) {
    // Handle other errors
}
```

## Best Practices

1. **Provider Selection**
   - Use OpenAI for general purpose tasks
   - Use Gemini for cost-effective operations
   - Use Claude for complex reasoning tasks

2. **Error Handling**
   - Always wrap API calls in try-catch blocks
   - Implement proper error handling
   - Consider implementing retries for transient errors

3. **Configuration**
   - Use environment variables for API keys
   - Adjust temperature based on task requirements
   - Set appropriate token limits

4. **Performance**
   - Use streaming for long responses
   - Implement caching where appropriate
   - Monitor API usage and costs

## Next Steps

1. Learn about [Memory Management](memory-management)
2. Explore [Streaming](streaming)
3. Read about [Error Handling](../advanced/error-handling)

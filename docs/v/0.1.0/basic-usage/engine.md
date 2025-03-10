# Basic Engine Usage

The Bupple AI Engine provides a simple interface for interacting with various AI engines. Here's how to use the basic features.

## Getting Started

First, import the Engine facade:

```php
use Bupple\Engine\Facades\Engine;
```

## Sending Messages

### Simple Message

```php
$response = Engine::engine()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

echo $response['content']; // AI's response
```

### Conversation with Context

```php
$messages = [
    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
    ['role' => 'user', 'content' => 'What is Laravel?'],
    ['role' => 'assistant', 'content' => 'Laravel is a PHP web framework.'],
    ['role' => 'user', 'content' => 'Tell me more about its features.']
];

$response = Engine::engine()->send($messages);
```

## Streaming Responses

For real-time responses, use the stream method:

```php
$stream = Engine::engine()->stream([
    ['role' => 'user', 'content' => 'Tell me a story']
]);

foreach ($stream as $chunk) {
    echo $chunk['content']; // Output each chunk as it arrives
    flush();
}
```

## Switching Engines

You can switch between different AI engines:

```php
// Use OpenAI
$openaiResponse = Engine::engine('openai')->send($messages);

// Use Gemini
$geminiResponse = Engine::engine('gemini')->send($messages);

// Use Claude
$claudeResponse = Engine::engine('claude')->send($messages);
```

## Response Format

The response from `send()` method includes:

```php
[
    'role' => 'assistant',
    'content' => 'The response text',
    'model' => 'The model used (e.g., gpt-4)',
    'usage' => [
        // Usage statistics (varies by provider)
    ]
]
```

## Error Handling

Basic error handling:

```php
try {
    $response = Engine::engine()->send($messages);
} catch (\RuntimeException $e) {
    // Handle API errors
    echo "Error: " . $e->getMessage();
} catch (\Exception $e) {
    // Handle other errors
    echo "Error: " . $e->getMessage();
}
```

## Configuration Access

You can access the engine configuration:

```php
// Get all config
$config = Engine::engine()->getConfig();

// Get specific engine config
$openaiConfig = Engine::engine('openai')->getConfig();
```

## Next Steps

For more advanced usage, including:
- Custom model parameters
- Media content handling
- Advanced error handling
- Custom engine implementations

See the [Advanced Engine Usage](../advanced-usage/engine.md) guide. 
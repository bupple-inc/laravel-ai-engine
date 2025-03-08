# Basic Usage

This guide provides practical examples of how to use the Bupple Laravel AI Engine in your application.

## Simple Chat Completion

The most basic usage is sending a chat completion request:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Simple chat completion
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

echo $response['content']; // AI's response
```

## Using Different Providers

You can specify which AI provider to use:

```php
// Using OpenAI
$openai = BuppleEngine::ai('openai')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Using Google Gemini
$gemini = BuppleEngine::ai('gemini')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);

// Using Anthropic Claude
$claude = BuppleEngine::ai('claude')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

## System Messages

Include system messages to set the context:

```php
$response = BuppleEngine::ai()->send([
    [
        'role' => 'system',
        'content' => 'You are a helpful assistant that specializes in Laravel development.'
    ],
    [
        'role' => 'user',
        'content' => 'What are Laravel service providers?'
    ]
]);
```

## Streaming Responses

Use streaming for real-time responses:

```php
// Using Server-Sent Events (SSE)
return response()->stream(function () {
    $stream = BuppleEngine::ai()->stream([
        ['role' => 'user', 'content' => 'Write a long story about...']
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

## Memory Management

Use the memory system to maintain conversation history:

```php
use Bupple\Engine\Facades\BuppleEngine;

// Initialize memory
$memory = BuppleEngine::memory();

// Set parent context (required)
$memory->setParent(User::class, auth()->id());

// Add user message to memory
$memory->addMessage('user', 'What is Laravel?');

// Get chat history
$messages = $memory->getMessages();

// Send chat completion with history
$response = BuppleEngine::ai()->send($messages);

// Store AI response in memory
$memory->addMessage('assistant', $response['content']);
```

## Dependency Injection

Use dependency injection in your controllers:

```php
use Bupple\Engine\BuppleEngine;

class ChatController extends Controller
{
    public function __construct(
        private BuppleEngine $engine
    ) {}

    public function chat(Request $request)
    {
        $response = $this->engine->ai()->send([
            ['role' => 'user', 'content' => $request->input('message')]
        ]);

        return response()->json([
            'message' => $response['content']
        ]);
    }
}
```

## Error Handling

Handle potential errors gracefully:

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = BuppleEngine::ai()->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (AiProviderException $e) {
    // Handle provider-specific errors
    report($e);
    return response()->json([
        'error' => 'AI service temporarily unavailable'
    ], 503);
} catch (\Exception $e) {
    // Handle other errors
    report($e);
    return response()->json([
        'error' => 'An unexpected error occurred'
    ], 500);
}
```

## Configuration Options

Customize the behavior with configuration options:

```php
// Set custom parameters for this request
$response = BuppleEngine::ai()
    ->withModel('gpt-4')
    ->withTemperature(0.8)
    ->withMaxTokens(2000)
    ->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);

// Use a specific memory driver
$memory = BuppleEngine::memory('mongodb');
```

## Next Steps

- [Memory Management Guide](/guide/memory-management) - Learn more about the memory system
- [Streaming Guide](/guide/streaming) - Advanced streaming techniques
- [API Reference](/api/overview) - Complete API documentation
- [Configuration](/guide/configuration) - All configuration options 
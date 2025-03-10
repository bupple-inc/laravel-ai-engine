# Basic SSE Usage

The Bupple AI Engine provides Server-Sent Events (SSE) support for real-time streaming of AI responses. Here's how to use the basic SSE features.

## Getting Started

First, import the Engine facade:

```php
use Bupple\Engine\Facades\Engine;
```

## Basic SSE Response

In your Laravel controller:

```php
use Illuminate\Http\Response;

public function stream()
{
    $messages = [
        ['role' => 'user', 'content' => 'Tell me a story']
    ];

    return response()->stream(function () use ($messages) {
        $stream = Engine::engine()->stream($messages);
        
        foreach ($stream as $chunk) {
            echo "data: " . json_encode($chunk) . "\n\n";
            ob_flush();
            flush();
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

## Using the SSE Driver

The SSE driver provides additional formatting and handling:

```php
public function streamWithDriver()
{
    $messages = [
        ['role' => 'user', 'content' => 'Tell me a story']
    ];

    return response()->stream(function () use ($messages) {
        $stream = Engine::engine()->stream($messages);
        
        foreach ($stream as $chunk) {
            Engine::sse()->send($chunk);
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

## Client-Side Implementation

JavaScript example for consuming the SSE stream:

```javascript
const eventSource = new EventSource('/api/stream');

eventSource.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log(data.content);
    // Append content to your UI
};

eventSource.onerror = (error) => {
    console.error('SSE Error:', error);
    eventSource.close();
};
```

## Error Handling

Handling errors in the stream:

```php
public function streamWithErrorHandling()
{
    return response()->stream(function () {
        try {
            $stream = Engine::engine()->stream($messages);
            
            foreach ($stream as $chunk) {
                Engine::sse()->send($chunk);
            }
        } catch (\Exception $e) {
            Engine::sse()->sendError($e->getMessage());
        } finally {
            Engine::sse()->end();
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

## SSE with Memory

Combining SSE with memory management:

```php
public function streamWithMemory()
{
    Engine::memory()->setParent('ChatSession', $sessionId);
    
    return response()->stream(function () {
        $messages = Engine::memory()->getMessages();
        $stream = Engine::engine()->stream($messages);
        
        foreach ($stream as $chunk) {
            Engine::sse()->send($chunk);
            // Optionally store the response
            Engine::memory()->addAssistantMessage($chunk['content']);
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

## Next Steps

For more advanced usage, including:
- Custom event types
- Progress tracking
- Connection management
- Advanced error handling

See the [Advanced SSE Usage](../advanced-usage/sse.md) guide. 
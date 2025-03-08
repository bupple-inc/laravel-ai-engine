# Streaming

The Bupple Laravel AI Engine provides Server-Sent Events (SSE) streaming capabilities for real-time AI responses.

## Overview

Streaming is particularly useful for:
- Long-form content generation
- Real-time chat interfaces
- Progress indicators
- Interactive applications

## Basic Usage

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get SSE driver
$sse = BuppleEngine::sse();

// Start SSE stream
$sse->start();

// Get streaming response from AI
$stream = BuppleEngine::ai()->stream([
    ['role' => 'user', 'content' => 'Write a story...']
]);

// Send each chunk
foreach ($stream as $chunk) {
    $sse->send($chunk['content']);
}

// End stream
$sse->end();
```

## Custom Headers

You can customize the SSE response headers:

```php
// Add custom headers
$sse->start([
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET',
    'Access-Control-Allow-Headers' => 'Content-Type',
]);
```

## Message Types

### Simple Text Message

```php
$sse->send('Hello, world!');
```

### JSON Message

```php
$sse->send([
    'type' => 'message',
    'content' => 'Hello, world!',
    'timestamp' => time(),
]);
```

### Custom Event Type

```php
$sse->send('Hello!', null, 'greeting');
```

### Message with ID

```php
$sse->send('Hello!', 'msg-123');
```

## Batch Messages

Send multiple messages at once:

```php
$sse->sendBatch([
    [
        'id' => 'msg-1',
        'event' => 'message',
        'data' => 'First message',
    ],
    [
        'id' => 'msg-2',
        'event' => 'update',
        'data' => 'Second message',
    ],
]);
```

## Keep-Alive

Send keep-alive messages to prevent connection timeout:

```php
// Send keep-alive with default comment
$sse->keepAlive();

// Send keep-alive with custom comment
$sse->keepAlive('Still processing...');
```

## Error Handling

```php
try {
    // Process request
    throw new \Exception('Something went wrong');
} catch (\Exception $e) {
    // Send error message
    $sse->sendError(
        'Failed to process request',
        500
    );
    
    // End stream
    $sse->end();
}
```

## Stream Control

### Auto-Flush Control

```php
// Disable auto-flush
$sse->setAutoFlush(false);

// Send multiple messages
$sse->send('First message', null, null, false);
$sse->send('Second message', null, null, false);
$sse->send('Last message', null, null, true);
```

### Retry Timeout

```php
// Set retry timeout (in milliseconds)
$sse->setRetryTimeout(5000);
```

## Example: Chat Application

Here's a complete example of using streaming in a chat application:

```php
use Bupple\Engine\Facades\BuppleEngine;

class ChatController extends Controller
{
    public function stream(Request $request)
    {
        // Initialize SSE
        $sse = BuppleEngine::sse();
        
        // Start stream with CORS headers
        $sse->start([
            'Access-Control-Allow-Origin' => '*',
        ]);
        
        try {
            // Get streaming response
            $stream = BuppleEngine::ai()->stream([
                ['role' => 'user', 'content' => $request->message],
            ]);
            
            // Send each chunk
            foreach ($stream as $chunk) {
                if ($chunk['done']) {
                    $sse->end($chunk['content']);
                } else {
                    $sse->send($chunk['content']);
                }
            }
        } catch (\Exception $e) {
            $sse->sendError('Failed to generate response', 500);
            $sse->end();
        }
    }
}
```

## Frontend Integration

### JavaScript Example

```javascript
const eventSource = new EventSource('/api/chat/stream');

eventSource.onmessage = (event) => {
    console.log('Received:', event.data);
};

eventSource.onerror = (error) => {
    console.error('Error:', error);
    eventSource.close();
};

// Listen for custom events
eventSource.addEventListener('error', (event) => {
    const error = JSON.parse(event.data);
    console.error('Server Error:', error.message);
});

eventSource.addEventListener('done', () => {
    eventSource.close();
});
```

### React Example

```jsx
import { useEffect, useState } from 'react';

function Chat() {
    const [messages, setMessages] = useState([]);
    
    useEffect(() => {
        const eventSource = new EventSource('/api/chat/stream');
        
        eventSource.onmessage = (event) => {
            setMessages(prev => [...prev, event.data]);
        };
        
        eventSource.addEventListener('error', (event) => {
            const error = JSON.parse(event.data);
            console.error('Error:', error.message);
        });
        
        eventSource.addEventListener('done', () => {
            eventSource.close();
        });
        
        return () => {
            eventSource.close();
        };
    }, []);
    
    return (
        <div className="chat">
            {messages.map((message, index) => (
                <div key={index} className="message">
                    {message}
                </div>
            ))}
        </div>
    );
}
```

## Best Practices

1. **Error Handling**
   - Always handle connection errors
   - Send appropriate error messages
   - Close connections properly

2. **Performance**
   - Use appropriate retry timeouts
   - Implement keep-alive for long connections
   - Close connections when done

3. **Security**
   - Validate input data
   - Set appropriate CORS headers
   - Handle authentication/authorization

4. **User Experience**
   - Show loading states
   - Handle reconnection gracefully
   - Provide feedback on errors

## Next Steps

1. Learn about [Memory Management](memory-management)
2. Explore [Error Handling](../advanced/error-handling)
3. Read about [Best Practices](../advanced/best-practices)

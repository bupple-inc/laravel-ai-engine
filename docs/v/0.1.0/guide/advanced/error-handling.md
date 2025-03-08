# Error Handling

The Bupple Laravel AI Engine provides comprehensive error handling for various operations.

## Common Exceptions

### Memory Operations

```php
try {
    $memory = BuppleEngine::memory()->driver('openai');
    $messages = $memory->getMessages();
} catch (\RuntimeException $e) {
    // Thrown when parent context is not set
    Log::error('Memory Error', [
        'message' => $e->getMessage(),
    ]);
} catch (\InvalidArgumentException $e) {
    // Thrown when using an unsupported memory driver
    Log::error('Driver Error', [
        'message' => $e->getMessage(),
    ]);
} catch (\Exception $e) {
    // Handle other errors
    Log::error('Unexpected Error', [
        'message' => $e->getMessage(),
    ]);
}
```

### AI Operations

```php
try {
    $response = BuppleEngine::ai()->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (\RuntimeException $e) {
    // Handle API errors
    Log::error('AI Error', [
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
    ]);
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    // Handle HTTP client errors
    Log::error('HTTP Error', [
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

### Streaming Operations

```php
try {
    $sse = BuppleEngine::sse();
    $sse->start();
    
    $stream = BuppleEngine::ai()->stream([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
    
    foreach ($stream as $chunk) {
        $sse->send($chunk['content']);
    }
} catch (\RuntimeException $e) {
    // Handle streaming errors
    $sse->sendError('Streaming failed', 500);
} catch (\Exception $e) {
    // Handle other errors
    $sse->sendError('Unexpected error', 500);
} finally {
    $sse->end();
}
```

## Specific Error Cases

### Memory Driver Errors

1. **Missing Parent Context**
```php
// Will throw RuntimeException
$memory->addMessage('user', 'Hello!');
```

2. **Invalid Driver**
```php
// Will throw InvalidArgumentException
BuppleEngine::memory()->driver('unsupported');
```

3. **Driver Class Issues**
```php
// Will throw RuntimeException if class doesn't exist
// Will throw RuntimeException if class doesn't implement interface
BuppleEngine::memory()->driver('custom');
```

### AI Provider Errors

1. **Authentication Errors**
```php
try {
    $response = BuppleEngine::ai('openai')->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
} catch (\RuntimeException $e) {
    if ($e->getCode() === 401) {
        Log::error('Authentication failed');
    }
}
```

2. **Rate Limit Errors**
```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (\RuntimeException $e) {
    if ($e->getCode() === 429) {
        Log::warning('Rate limit exceeded');
    }
}
```

3. **Invalid Configuration**
```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (\InvalidArgumentException $e) {
    Log::error('Invalid configuration');
}
```

### Streaming Errors

1. **Connection Errors**
```php
$sse = BuppleEngine::sse();
try {
    $sse->start();
    if (connection_aborted()) {
        throw new \RuntimeException('Connection aborted');
    }
} catch (\Exception $e) {
    $sse->sendError('Connection failed', 500);
    $sse->end();
}
```

2. **Buffer Errors**
```php
try {
    $sse->send($data);
    if (!ob_get_length()) {
        throw new \RuntimeException('Buffer error');
    }
} catch (\Exception $e) {
    $sse->sendError('Failed to send data', 500);
}
```

## Error Response Format

### AI Response Errors
```php
[
    'error' => true,
    'code' => 500,
    'message' => 'Error message',
    'details' => [
        'provider' => 'openai',
        'request_id' => '123',
    ],
]
```

### Streaming Errors
```php
[
    'error' => true,
    'message' => 'Error message',
    'code' => 500,
]
```

## Best Practices

1. **Error Logging**
   - Log all errors with appropriate context
   - Use different log levels for different errors
   - Include request/response data when relevant

2. **Error Recovery**
   - Implement retry mechanisms for transient errors
   - Provide fallback options when possible
   - Clean up resources in finally blocks

3. **User Feedback**
   - Return user-friendly error messages
   - Include appropriate HTTP status codes
   - Provide guidance on error resolution

4. **Monitoring**
   - Track error rates and patterns
   - Set up alerts for critical errors
   - Monitor API provider status

## Example: Complete Error Handling

```php
use Bupple\Engine\Facades\BuppleEngine;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        try {
            // Initialize memory
            $memory = BuppleEngine::memory()->driver();
            
            // Add user message
            $memory->addUserMessage($request->message);
            
            // Get conversation history
            $messages = $memory->getMessages();
            
            // Get AI response
            $response = BuppleEngine::ai()->send($messages);
            
            // Store AI response
            $memory->addAssistantMessage($response['content']);
            
            return response()->json([
                'message' => $response['content']
            ]);
            
        } catch (\RuntimeException $e) {
            Log::error('AI Error', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'user_message' => $request->message,
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Failed to process your request',
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Unexpected Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
```

## Next Steps

1. Learn about [Best Practices](best-practices)
2. Explore [Memory Management](../core/memory-management)
3. Read about [Streaming](../core/streaming)

# Error Handling

This guide explains how to handle various errors and exceptions in the Bupple Laravel AI Engine.

## Exception Types

### AiProviderException

Thrown when there are issues with AI provider interactions:

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    $provider = $e->getProvider();    // The AI provider that failed
    $message = $e->getMessage();      // Error message
    $code = $e->getCode();           // Error code
}
```

Common scenarios:
- Invalid API key
- Rate limit exceeded
- Model not found
- Invalid request format

### MemoryException

Thrown when there are issues with memory operations:

```php
use Bupple\Engine\Exceptions\MemoryException;

try {
    $memory = BuppleEngine::memory();
    $memory->setParent(User::class, auth()->id());
    $messages = $memory->getMessages();
} catch (MemoryException $e) {
    // Handle memory-related errors
}
```

Common scenarios:
- Database connection issues
- Parent context not set
- Invalid message format

### ConfigurationException

Thrown when there are configuration-related issues:

```php
use Bupple\Engine\Exceptions\ConfigurationException;

try {
    $ai = BuppleEngine::ai('invalid-provider');
} catch (ConfigurationException $e) {
    // Handle configuration errors
}
```

Common scenarios:
- Invalid provider specified
- Missing required configuration
- Invalid configuration values

## Error Handling Strategies

### Basic Error Handling

```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    // Handle provider errors
    report($e);
    return response()->json([
        'error' => 'AI service temporarily unavailable'
    ], 503);
} catch (MemoryException $e) {
    // Handle memory errors
    report($e);
    return response()->json([
        'error' => 'Memory service unavailable'
    ], 503);
} catch (\Exception $e) {
    // Handle other errors
    report($e);
    return response()->json([
        'error' => 'An unexpected error occurred'
    ], 500);
}
```

### Provider Fallback

Implement fallback to different providers:

```php
try {
    // Try primary provider
    $response = BuppleEngine::ai('openai')->send($messages);
} catch (AiProviderException $e) {
    try {
        // Fallback to secondary provider
        $response = BuppleEngine::ai('gemini')->send($messages);
    } catch (AiProviderException $e) {
        // Fallback to tertiary provider
        $response = BuppleEngine::ai('claude')->send($messages);
    }
}
```

### Retry Logic

Implement retry logic for transient errors:

```php
function sendWithRetry($messages, $maxRetries = 3, $delay = 1000)
{
    $attempts = 0;
    
    while ($attempts < $maxRetries) {
        try {
            return BuppleEngine::ai()->send($messages);
        } catch (AiProviderException $e) {
            $attempts++;
            
            if ($attempts === $maxRetries) {
                throw $e;
            }
            
            // Wait before retrying
            usleep($delay * 1000);
        }
    }
}
```

### Streaming Error Handling

Handle errors in streaming responses:

```php
return response()->stream(function () {
    try {
        $stream = BuppleEngine::ai()->stream([
            ['role' => 'user', 'content' => 'Write a story...']
        ]);

        foreach ($stream as $chunk) {
            if (connection_aborted()) {
                break;
            }

            echo "data: " . json_encode([
                'content' => $chunk['content']
            ]) . "\n\n";
            
            ob_flush();
            flush();
        }
    } catch (AiProviderException $e) {
        echo "data: " . json_encode([
            'error' => true,
            'message' => 'AI provider error: ' . $e->getMessage()
        ]) . "\n\n";
    } catch (\Exception $e) {
        echo "data: " . json_encode([
            'error' => true,
            'message' => 'An unexpected error occurred'
        ]) . "\n\n";
    }
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]);
```

### Memory Error Recovery

Handle memory errors with fallback strategies:

```php
try {
    $memory = BuppleEngine::memory();
    $memory->setParent(User::class, auth()->id());
    $messages = $memory->getMessages();
} catch (MemoryException $e) {
    // Fallback to empty conversation
    $messages = [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant'
        ]
    ];
}
```

## Logging and Monitoring

### Error Logging

```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    Log::error('AI Provider Error', [
        'provider' => $e->getProvider(),
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

### Usage Monitoring

```php
try {
    $response = BuppleEngine::ai()->send($messages);
    
    // Log usage statistics
    Log::info('AI Request Success', [
        'model' => $response['model'],
        'tokens' => $response['usage']['total_tokens'],
        'provider' => $response['provider']
    ]);
} catch (\Exception $e) {
    // Log failure
    Log::error('AI Request Failed', [
        'error' => $e->getMessage()
    ]);
}
```

## Best Practices

1. **Graceful Degradation**:
   - Always have fallback options
   - Provide meaningful user feedback
   - Maintain service availability

2. **Error Messages**:
   - Use user-friendly error messages
   - Log detailed error information
   - Include relevant context

3. **Rate Limiting**:
   - Implement proper rate limiting
   - Handle rate limit errors gracefully
   - Use exponential backoff

4. **Monitoring**:
   - Log all errors and exceptions
   - Monitor usage patterns
   - Track error rates

## Common Error Scenarios

### API Key Issues

```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    if (str_contains($e->getMessage(), 'API key')) {
        Log::critical('Invalid API key', [
            'provider' => $e->getProvider()
        ]);
        
        return response()->json([
            'error' => 'Service configuration error'
        ], 503);
    }
}
```

### Rate Limits

```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    if (str_contains($e->getMessage(), 'rate limit')) {
        // Wait and retry
        sleep(1);
        return BuppleEngine::ai()->send($messages);
    }
}
```

### Connection Issues

```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (AiProviderException $e) {
    if ($e->getCode() === CURLE_OPERATION_TIMEOUTED) {
        // Handle timeout
        return response()->json([
            'error' => 'Service temporarily unavailable'
        ], 503);
    }
}
``` 
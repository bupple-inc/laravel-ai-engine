# Error Handling

This guide covers comprehensive error handling strategies for the Bupple AI Engine.

## Engine Errors

### Basic Error Handling

```php
use Bupple\Engine\Facades\Engine;
use Bupple\Engine\Core\Drivers\Engine\Exceptions\EngineException;
use Bupple\Engine\Core\Drivers\Engine\Exceptions\RateLimitException;
use Bupple\Engine\Core\Drivers\Engine\Exceptions\AuthenticationException;

try {
    $response = Engine::engine()->send($messages);
} catch (RateLimitException $e) {
    // Handle rate limiting
    Log::warning('Rate limit exceeded', [
        'error' => $e->getMessage(),
        'retry_after' => $e->getRetryAfter()
    ]);
    // Implement backoff strategy
    sleep($e->getRetryAfter());
} catch (AuthenticationException $e) {
    // Handle authentication errors
    Log::error('Authentication failed', [
        'error' => $e->getMessage()
    ]);
    // Refresh credentials or notify admin
} catch (EngineException $e) {
    // Handle other engine-specific errors
    Log::error('Engine error', [
        'error' => $e->getMessage(),
        'engine' => Engine::engine()->getConfig()['driver']
    ]);
} catch (\Exception $e) {
    // Handle unexpected errors
    Log::error('Unexpected error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

### Fallback Strategy

```php
class EngineService
{
    protected $fallbackEngines = ['openai', 'gemini', 'claude'];
    protected $maxRetries = 3;

    public function sendWithFallback(array $messages)
    {
        foreach ($this->fallbackEngines as $engine) {
            $retryCount = 0;
            
            while ($retryCount < $this->maxRetries) {
                try {
                    return Engine::engine($engine)->send($messages);
                } catch (RateLimitException $e) {
                    $retryCount++;
                    if ($retryCount >= $this->maxRetries) {
                        continue 2; // Try next engine
                    }
                    sleep($e->getRetryAfter());
                } catch (AuthenticationException $e) {
                    continue 2; // Try next engine
                } catch (EngineException $e) {
                    $retryCount++;
                    if ($retryCount >= $this->maxRetries) {
                        continue 2; // Try next engine
                    }
                    sleep(1); // Basic backoff
                }
            }
        }

        throw new \RuntimeException('All engines failed');
    }
}
```

## Memory Errors

### Memory Driver Errors

```php
use Bupple\Engine\Core\Drivers\Memory\Exceptions\MemoryException;
use Bupple\Engine\Core\Drivers\Memory\Exceptions\ConnectionException;
use Bupple\Engine\Core\Drivers\Memory\Exceptions\StorageException;

try {
    Engine::memory()->addMessage('user', 'Hello!');
} catch (ConnectionException $e) {
    // Handle database/redis connection errors
    Log::error('Memory connection error', [
        'error' => $e->getMessage(),
        'driver' => Engine::memory()->getConfig()['driver']
    ]);
    
    // Fallback to file driver
    Engine::memory()->driver('file')->addMessage('user', 'Hello!');
} catch (StorageException $e) {
    // Handle storage-related errors
    Log::error('Memory storage error', [
        'error' => $e->getMessage(),
        'driver' => Engine::memory()->getConfig()['driver']
    ]);
} catch (MemoryException $e) {
    // Handle other memory-specific errors
    Log::error('Memory error', [
        'error' => $e->getMessage()
    ]);
}
```

### Memory Recovery

```php
class MemoryRecoveryService
{
    public function recoverMessages(string $parentClass, string|int $parentId)
    {
        try {
            // Try primary driver
            return Engine::memory()
                ->setParent($parentClass, $parentId)
                ->getMessages();
        } catch (ConnectionException $e) {
            // Try backup drivers
            foreach (['file', 'database', 'redis'] as $driver) {
                try {
                    return Engine::memory()
                        ->driver($driver)
                        ->setParent($parentClass, $parentId)
                        ->getMessages();
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // If all drivers fail, return empty array
            return [];
        }
    }

    public function backupMessages(array $messages)
    {
        // Store to multiple drivers for redundancy
        foreach (['file', 'database', 'redis'] as $driver) {
            try {
                $memory = Engine::memory()->driver($driver);
                foreach ($messages as $message) {
                    $memory->addMessage(
                        $message['role'],
                        $message['content'],
                        $message['type'] ?? 'text',
                        $message['metadata'] ?? []
                    );
                }
            } catch (\Exception $e) {
                Log::warning("Backup to {$driver} failed", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
```

## SSE Errors

### Stream Error Handling

```php
public function streamWithErrorRecovery()
{
    return response()->stream(function () {
        $retryCount = 0;
        $maxRetries = 3;
        $lastPosition = 0;

        while ($retryCount < $maxRetries) {
            try {
                $stream = Engine::engine()->stream($messages);
                
                foreach ($stream as $index => $chunk) {
                    if ($index < $lastPosition) {
                        continue; // Skip already sent chunks
                    }
                    
                    Engine::sse()->send($chunk);
                    $lastPosition = $index;
                }
                
                break; // Success, exit loop
            } catch (\Exception $e) {
                $retryCount++;
                
                if ($retryCount >= $maxRetries) {
                    Engine::sse()->sendError([
                        'error' => 'Stream failed after retries',
                        'message' => $e->getMessage()
                    ]);
                    break;
                }

                // Notify client of retry
                Engine::sse()->send([
                    'type' => 'retry',
                    'attempt' => $retryCount,
                    'max_attempts' => $maxRetries
                ], 'retry');

                sleep(1 * $retryCount); // Exponential backoff
            }
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

### Client-Side Error Recovery

```javascript
class SSEClient {
    constructor(url, options = {}) {
        this.url = url;
        this.options = {
            maxRetries: 3,
            backoffMultiplier: 1.5,
            initialRetryDelay: 1000,
            ...options
        };
        
        this.retryCount = 0;
        this.retryDelay = this.options.initialRetryDelay;
        this.chunks = [];
        
        this.connect();
    }

    connect() {
        this.eventSource = new EventSource(this.url);
        this.setupHandlers();
    }

    setupHandlers() {
        this.eventSource.addEventListener('content', (event) => {
            const chunk = JSON.parse(event.data);
            this.chunks.push(chunk);
            this.options.onContent?.(chunk);
        });

        this.eventSource.addEventListener('retry', (event) => {
            const data = JSON.parse(event.data);
            console.log(`Retry attempt ${data.attempt} of ${data.max_attempts}`);
        });

        this.eventSource.addEventListener('error', (event) => {
            this.handleError(event);
        });
    }

    handleError(event) {
        if (this.retryCount < this.options.maxRetries) {
            this.retryCount++;
            this.retryDelay *= this.options.backoffMultiplier;
            
            console.log(`Reconnecting in ${this.retryDelay}ms...`);
            
            setTimeout(() => {
                this.eventSource.close();
                this.connect();
            }, this.retryDelay);
        } else {
            console.error('Max retries reached');
            this.eventSource.close();
            this.options.onMaxRetriesReached?.();
        }
    }

    close() {
        this.eventSource.close();
    }
}

// Usage
const sseClient = new SSEClient('/api/stream', {
    maxRetries: 5,
    backoffMultiplier: 2,
    initialRetryDelay: 1000,
    onContent: (chunk) => {
        console.log('Received chunk:', chunk);
    },
    onMaxRetriesReached: () => {
        console.error('Stream failed after max retries');
        showErrorToUser('Connection lost. Please try again later.');
    }
});
```

## Logging and Monitoring

### Error Logging Service

```php
class EngineErrorLogger
{
    public function logError(\Exception $e, array $context = [])
    {
        $data = [
            'error_type' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'context' => $context,
            'timestamp' => now(),
        ];

        // Log to file
        Log::error('Engine error', $data);

        // Store in database for monitoring
        EngineError::create($data);

        // Send notification if critical
        if ($this->isCriticalError($e)) {
            $this->notifyAdmins($data);
        }
    }

    protected function isCriticalError(\Exception $e): bool
    {
        return $e instanceof AuthenticationException ||
            $e instanceof ConnectionException ||
            $e->getCode() >= 500;
    }

    protected function notifyAdmins(array $data): void
    {
        Notification::route('slack', config('engine.error_webhook'))
            ->notify(new CriticalEngineError($data));
    }
}
```

## Next Steps

For more advanced topics, check out:
- [Advanced Engine Usage](./engine.md)
- [Advanced Memory Usage](./memory.md)
- [Advanced SSE Usage](./sse.md) 
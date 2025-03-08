# Best Practices

This guide outlines best practices for using the Bupple Laravel AI Engine effectively in your applications.

## General Guidelines

### 1. Configuration Management

```php
// Use environment variables for sensitive data
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'organization_id' => env('OPENAI_ORGANIZATION_ID'),
],

// Use configuration files for defaults
'default' => [
    'chat' => env('BUPPLE_CHAT_DRIVER', 'openai'),
    'memory' => env('BUPPLE_MEMORY_DRIVER', 'openai'),
],

// Create a configuration service provider if needed
class AIConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('ai.config', function ($app) {
            return new AIConfigManager($app['config']['bupple-engine']);
        });
    }
}
```

### 2. Service Layer Pattern

```php
class AIService
{
    protected BuppleEngine $engine;
    protected MemoryManager $memory;
    
    public function __construct(BuppleEngine $engine)
    {
        $this->engine = $engine;
        $this->memory = $engine->memory();
    }
    
    public function chat(string $message, string $conversationId): array
    {
        $this->memory->setParent('conversation', $conversationId);
        $this->memory->addUserMessage($message);
        
        $messages = $this->memory->getMessages();
        $response = $this->engine->ai()->send($messages);
        
        $this->memory->addAssistantMessage($response['content']);
        
        return $response;
    }
}
```

### 3. Rate Limiting

```php
// Implement rate limiting middleware
class AIRateLimitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'ai_rate_limit:' . auth()->id();
        
        if (Redis::get($key) >= config('ai.rate_limit')) {
            throw new TooManyRequestsHttpException();
        }
        
        Redis::incr($key);
        Redis::expire($key, 60); // Reset after 1 minute
        
        return $next($request);
    }
}
```

## Memory Management

### 1. Parent Context Organization

```php
// Group by feature
$memory->setParent('chat', $chatId);
$memory->setParent('document', $documentId);
$memory->setParent('analysis', $analysisId);

// Group by user
$memory->setParent(User::class, $userId);
$memory->setParent('user.chat', $userId . ':' . $chatId);
$memory->setParent('user.preferences', $userId . ':preferences');

// Group by team/organization
$memory->setParent('org.chat', $orgId . ':' . $chatId);
$memory->setParent('team.analysis', $teamId . ':' . $analysisId);
```

### 2. Memory Cleanup

```php
// Create a cleanup job
class CleanupOldMemories implements ShouldQueue
{
    public function handle()
    {
        // Delete memories older than 30 days
        Memory::where('created_at', '<', now()->subDays(30))->delete();
        
        // Or by conversation
        Memory::where('parent_class', 'conversation')
            ->where('updated_at', '<', now()->subDays(7))
            ->delete();
    }
}

// Schedule the cleanup
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CleanupOldMemories())->daily();
    }
}
```

### 3. Memory Optimization

```php
// Implement memory summarization
class MemoryOptimizer
{
    public function summarize(string $conversationId)
    {
        $memory = BuppleEngine::memory();
        $memory->setParent('conversation', $conversationId);
        
        $messages = $memory->getMessages();
        
        if (count($messages) > 50) {
            $summary = BuppleEngine::ai()->send([
                [
                    'role' => 'system',
                    'content' => 'Summarize this conversation concisely.'
                ],
                ...$messages
            ]);
            
            $memory->clear();
            $memory->addSystemMessage($summary['content']);
        }
    }
}
```

## Error Handling

### 1. Graceful Degradation

```php
class AIFallbackService
{
    public function withFallback(callable $primary, callable $fallback)
    {
        try {
            return $primary();
        } catch (\Exception $e) {
            Log::warning('AI Primary Service Failed', [
                'error' => $e->getMessage(),
            ]);
            
            try {
                return $fallback();
            } catch (\Exception $e) {
                Log::error('AI Fallback Service Failed', [
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }
}

// Usage
$service = new AIFallbackService();
$response = $service->withFallback(
    fn() => BuppleEngine::ai('openai')->send($messages),
    fn() => BuppleEngine::ai('gemini')->send($messages)
);
```

### 2. Circuit Breaker

```php
class AICircuitBreaker
{
    protected string $key = 'ai_circuit_breaker';
    protected int $threshold = 5;
    protected int $timeout = 300;
    
    public function execute(callable $operation)
    {
        if ($this->isOpen()) {
            throw new ServiceUnavailableException('AI service is temporarily unavailable');
        }
        
        try {
            $result = $operation();
            $this->reset();
            return $result;
        } catch (\Exception $e) {
            $this->recordFailure();
            throw $e;
        }
    }
    
    protected function isOpen(): bool
    {
        return Redis::get($this->key) >= $this->threshold;
    }
    
    protected function recordFailure(): void
    {
        Redis::incr($this->key);
        Redis::expire($this->key, $this->timeout);
    }
    
    protected function reset(): void
    {
        Redis::del($this->key);
    }
}
```

## Performance Optimization

### 1. Response Caching

```php
class AICacheService
{
    public function getCachedResponse(array $messages, int $ttl = 3600)
    {
        $key = 'ai_response:' . md5(serialize($messages));
        
        return Cache::remember($key, $ttl, function () use ($messages) {
            return BuppleEngine::ai()->send($messages);
        });
    }
    
    public function invalidateCache(array $messages): void
    {
        $key = 'ai_response:' . md5(serialize($messages));
        Cache::forget($key);
    }
}
```

### 2. Batch Processing

```php
class AIBatchProcessor
{
    public function processBatch(array $items, int $batchSize = 10)
    {
        $results = [];
        $chunks = array_chunk($items, $batchSize);
        
        foreach ($chunks as $chunk) {
            $promises = [];
            
            foreach ($chunk as $item) {
                $promises[] = async(function () use ($item) {
                    return BuppleEngine::ai()->send($item);
                });
            }
            
            $results = array_merge($results, await($promises));
        }
        
        return $results;
    }
}
```

### 3. Queue Processing

```php
class ProcessAIRequest implements ShouldQueue
{
    public function handle()
    {
        return BuppleEngine::ai()->send($this->messages);
    }
    
    public function backoff()
    {
        return [1, 5, 10]; // Retry delays in seconds
    }
    
    public function tags()
    {
        return ['ai', 'processing'];
    }
}
```

## Security

### 1. Input Validation

```php
class AIRequestValidator
{
    public function validate(array $messages): bool
    {
        $rules = [
            '*.role' => ['required', 'string', 'in:system,user,assistant'],
            '*.content' => ['required', 'string', 'max:4000'],
            '*.type' => ['sometimes', 'string', 'in:text,image,audio'],
        ];
        
        return Validator::make($messages, $rules)->passes();
    }
}
```

### 2. Output Sanitization

```php
class AIResponseSanitizer
{
    public function sanitize(array $response): array
    {
        return [
            'content' => strip_tags($response['content']),
            'role' => $response['role'],
            'model' => $response['model'],
        ];
    }
}
```

### 3. Access Control

```php
class AIAccessControl
{
    public function authorize(string $action, User $user): bool
    {
        return match ($action) {
            'chat' => $user->hasPermission('ai.chat'),
            'stream' => $user->hasPermission('ai.stream'),
            'memory' => $user->hasPermission('ai.memory'),
            default => false,
        };
    }
}
```

## Monitoring

### 1. Usage Tracking

```php
class AIUsageTracker
{
    public function track(string $provider, array $response): void
    {
        AIUsage::create([
            'provider' => $provider,
            'user_id' => auth()->id(),
            'tokens' => $response['usage']['total_tokens'] ?? 0,
            'model' => $response['model'],
            'cost' => $this->calculateCost($provider, $response),
        ]);
    }
}
```

### 2. Performance Monitoring

```php
class AIPerformanceMonitor
{
    public function measure(callable $operation): array
    {
        $start = microtime(true);
        $memory = memory_get_usage();
        
        try {
            $result = $operation();
            
            return [
                'success' => true,
                'duration' => microtime(true) - $start,
                'memory' => memory_get_usage() - $memory,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'duration' => microtime(true) - $start,
                'memory' => memory_get_usage() - $memory,
                'error' => $e->getMessage(),
            ];
        }
    }
}
```

## Testing

### 1. Mock Responses

```php
class AITestCase extends TestCase
{
    protected function mockAIResponse(array $response)
    {
        $this->mock(BuppleEngine::class, function ($mock) use ($response) {
            $mock->shouldReceive('ai->send')
                ->andReturn($response);
        });
    }
}
```

### 2. Feature Tests

```php
class AIChatTest extends TestCase
{
    public function test_chat_completion()
    {
        $this->mockAIResponse([
            'role' => 'assistant',
            'content' => 'Test response',
        ]);
        
        $response = $this->postJson('/api/chat', [
            'message' => 'Hello',
        ]);
        
        $response->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data' => ['content', 'role'],
            ]);
    }
}
```

## Next Steps

1. Explore [Error Handling](error-handling)
2. Learn about [Memory Management](../core/memory-management)
3. Read about [Streaming](../core/streaming)

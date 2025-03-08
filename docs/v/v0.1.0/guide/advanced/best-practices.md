# Best Practices

This guide outlines best practices for using the Bupple Laravel AI Engine effectively and securely.

## Security

### API Key Management

1. **Environment Variables**:
   - Store API keys in `.env` file
   - Never commit API keys to version control
   - Use Laravel's encrypted environment files in production

```php
// .env
OPENAI_API_KEY=your-api-key
GEMINI_API_KEY=your-api-key
CLAUDE_API_KEY=your-api-key
```

2. **Key Rotation**:
   - Regularly rotate API keys
   - Monitor API key usage
   - Implement key expiration policies

### Request Validation

1. **Input Sanitization**:
```php
use Illuminate\Support\Str;

$userInput = Str::of($request->input('message'))
    ->trim()
    ->limit(4000); // Prevent token limit issues
```

2. **Content Filtering**:
```php
if (containsSensitiveData($userInput)) {
    return response()->json([
        'error' => 'Message contains sensitive data'
    ], 422);
}
```

## Performance

### Model Selection

1. **Task-Based Selection**:
   ```php
   // Complex tasks
   $response = BuppleEngine::ai()
       ->withModel('gpt-4')
       ->send($messages);

   // Simple tasks
   $response = BuppleEngine::ai()
       ->withModel('gpt-3.5-turbo')
       ->send($messages);
   ```

2. **Cost-Performance Balance**:
   ```php
   // Cost-effective configuration
   $response = BuppleEngine::ai()
       ->withModel('gemini-pro')
       ->withMaxTokens(500)
       ->send($messages);
   ```

### Caching

1. **Response Caching**:
```php
use Illuminate\Support\Facades\Cache;

$response = Cache::remember("ai_response_{$hash}", 3600, function () use ($messages) {
    return BuppleEngine::ai()->send($messages);
});
```

2. **Partial Results**:
```php
$chunks = Cache::tags(['ai_responses'])->rememberForever($key, function () {
    return BuppleEngine::ai()->stream($messages)->toArray();
});
```

## Memory Management

### Context Management

1. **Parent Context**:
```php
$memory = BuppleEngine::memory();
$memory->setParent(User::class, auth()->id());
```

2. **Memory Cleanup**:
```php
// Cleanup old conversations
$memory->cleanup(now()->subDays(30));
```

### Database Optimization

1. **Indexing**:
```php
// In your migration
Schema::create('memories', function (Blueprint $table) {
    $table->id();
    $table->string('parent_type')->index();
    $table->unsignedBigInteger('parent_id')->index();
    $table->json('messages');
    $table->timestamps();
    
    $table->index(['parent_type', 'parent_id']);
});
```

2. **Batch Processing**:
```php
$memory->batch()->process($messages);
```

## Error Handling

### Graceful Degradation

1. **Provider Fallback**:
```php
try {
    $response = BuppleEngine::ai('openai')->send($messages);
} catch (AiProviderException $e) {
    try {
        // Fallback to alternative provider
        $response = BuppleEngine::ai('gemini')->send($messages);
    } catch (AiProviderException $e) {
        report($e);
        return $fallbackResponse;
    }
}
```

2. **Rate Limiting**:
```php
use Illuminate\Support\Facades\RateLimiter;

$executed = RateLimiter::attempt(
    'ai_requests',
    $maxAttempts = 60,
    function() use ($messages) {
        return BuppleEngine::ai()->send($messages);
    },
    $decaySeconds = 60
);
```

## Monitoring

### Logging

1. **Request Logging**:
```php
Log::info('AI Request', [
    'provider' => $provider,
    'model' => $model,
    'tokens' => $response['usage']['total_tokens']
]);
```

2. **Error Tracking**:
```php
try {
    $response = BuppleEngine::ai()->send($messages);
} catch (Exception $e) {
    Log::error('AI Error', [
        'message' => $e->getMessage(),
        'provider' => $e->getProvider(),
        'trace' => $e->getTraceAsString()
    ]);
}
```

### Metrics

1. **Usage Tracking**:
```php
$metrics = [
    'total_tokens' => $response['usage']['total_tokens'],
    'response_time' => $endTime - $startTime,
    'success_rate' => $successfulRequests / $totalRequests
];

MetricsCollector::record($metrics);
```

2. **Cost Monitoring**:
```php
$cost = calculateCost(
    $response['usage']['total_tokens'],
    $response['model']
);

CostTracker::record($cost);
```

## Testing

### Unit Tests

1. **Mock Responses**:
```php
use Tests\TestCase;
use Mockery;

class AiTest extends TestCase
{
    public function test_ai_response()
    {
        $mock = Mockery::mock(BuppleEngine::class);
        $mock->shouldReceive('send')
            ->once()
            ->andReturn([
                'content' => 'Test response',
                'usage' => ['total_tokens' => 10]
            ]);
            
        $this->app->instance(BuppleEngine::class, $mock);
        
        // Test your implementation
    }
}
```

2. **Provider Tests**:
```php
public function test_provider_fallback()
{
    $response = BuppleEngine::ai()
        ->withFallback(['openai', 'gemini', 'claude'])
        ->send($messages);
        
    $this->assertNotNull($response['content']);
}
```

## Deployment

### Configuration Management

1. **Environment Specific**:
```php
// config/bupple-engine.php
return [
    'default' => [
        'chat' => env('BUPPLE_CHAT_DRIVER', 'openai'),
        'memory' => env('BUPPLE_MEMORY_DRIVER', 'mysql'),
    ],
    'timeout' => env('BUPPLE_TIMEOUT', 30),
];
```

2. **Provider Settings**:
```php
// Production settings
'openai' => [
    'retry_attempts' => 3,
    'timeout' => 60,
    'ssl_verify' => true,
],
```

### Queue Configuration

1. **Long-Running Tasks**:
```php
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessAiRequest implements ShouldQueue
{
    public function handle()
    {
        $response = BuppleEngine::ai()
            ->withTimeout(120)
            ->send($this->messages);
    }
}
```

2. **Error Handling**:
```php
public function failed(Throwable $exception)
{
    Log::error('AI Queue Job Failed', [
        'exception' => $exception->getMessage(),
        'messages' => $this->messages
    ]);
    
    // Notify administrators
    Notification::route('slack', env('SLACK_WEBHOOK'))
        ->notify(new AiJobFailedNotification($exception));
}
``` 
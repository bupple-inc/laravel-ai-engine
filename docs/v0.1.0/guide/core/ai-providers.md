# AI Providers

The Bupple Laravel AI Engine supports multiple AI providers, each with its own unique capabilities and configuration options.

## Supported Providers

### OpenAI

OpenAI integration provides access to GPT-4 and GPT-3.5 models.

#### Configuration
```env
# OpenAI Configuration
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4
OPENAI_TEMPERATURE=0.7
OPENAI_MAX_TOKENS=1000
OPENAI_ORGANIZATION_ID=       # Optional
```

#### Usage
```php
use Bupple\Engine\Facades\BuppleEngine;

// Using default configuration
$openai = BuppleEngine::ai('openai');

// Custom configuration for this request
$response = $openai
    ->withModel('gpt-4')
    ->withTemperature(0.8)
    ->withMaxTokens(2000)
    ->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
```

### Google Gemini

Google Gemini integration provides access to the Gemini Pro model.

#### Configuration
```env
# Google Gemini Configuration
GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-pro
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=1000
GEMINI_PROJECT_ID=            # Optional
```

#### Usage
```php
// Using default configuration
$gemini = BuppleEngine::ai('gemini');

// Custom configuration for this request
$response = $gemini
    ->withModel('gemini-pro')
    ->withTemperature(0.5)
    ->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
```

### Anthropic Claude

Anthropic Claude integration provides access to Claude 3 models.

#### Configuration
```env
# Anthropic Claude Configuration
CLAUDE_API_KEY=your-claude-api-key
CLAUDE_MODEL=claude-3-opus-20240229
CLAUDE_TEMPERATURE=0.7
CLAUDE_MAX_TOKENS=1000
```

#### Usage
```php
// Using default configuration
$claude = BuppleEngine::ai('claude');

// Custom configuration for this request
$response = $claude
    ->withModel('claude-3-opus-20240229')
    ->withTemperature(0.6)
    ->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
```

## Common Features

All providers support these common features:

### Message Format

```php
[
    [
        'role' => 'system',
        'content' => 'You are a helpful assistant'
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?'
    ]
]
```

### Response Format

```php
[
    'role' => 'assistant',
    'content' => string,    // The response text
    'model' => string,      // The model used
    'usage' => [           // Token usage information
        'prompt_tokens' => int,
        'completion_tokens' => int,
        'total_tokens' => int
    ]
]
```

### Streaming Support

All providers support streaming responses:

```php
$stream = BuppleEngine::ai('openai')->stream([
    ['role' => 'user', 'content' => 'Write a story...']
]);

foreach ($stream as $chunk) {
    echo $chunk['content'];
}
```

## Provider Selection

### Default Provider

Set the default provider in your `.env` file:

```env
BUPPLE_CHAT_DRIVER=openai  # Options: openai, gemini, claude
```

### Runtime Selection

```php
// Using specific provider
$ai = BuppleEngine::ai('openai');  // or 'gemini', 'claude'

// Using default provider
$ai = BuppleEngine::ai();
```

## Best Practices

### Model Selection

1. **OpenAI Models**:
   - `gpt-4`: Best for complex tasks requiring high accuracy
   - `gpt-3.5-turbo`: Good for simpler tasks, more cost-effective

2. **Gemini Models**:
   - `gemini-pro`: Balanced performance and cost

3. **Claude Models**:
   - `claude-3-opus-20240229`: High-quality responses for specialized tasks

### Temperature Settings

- **Low (0.1-0.3)**: More focused, deterministic responses
- **Medium (0.4-0.7)**: Balanced creativity and accuracy
- **High (0.8-1.0)**: More creative, varied responses

### Token Management

- Monitor token usage through response metadata
- Set appropriate `max_tokens` for your use case
- Consider cost implications of different models

### Error Handling

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = BuppleEngine::ai('openai')->send($messages);
} catch (AiProviderException $e) {
    // Handle provider-specific errors
    $provider = $e->getProvider();
    $message = $e->getMessage();
    $code = $e->getCode();
}
```

## Provider-Specific Features

### OpenAI

- Organization support via `OPENAI_ORGANIZATION_ID`
- Comprehensive token usage statistics
- Advanced model parameter control

### Gemini

- Project-based isolation via `GEMINI_PROJECT_ID`
- Efficient token usage
- Cost-effective processing

### Claude

- Advanced context understanding
- High-quality responses
- Specialized task capabilities

## Examples

### Provider Fallback

```php
try {
    $response = BuppleEngine::ai('openai')->send($messages);
} catch (AiProviderException $e) {
    // Try fallback provider
    $response = BuppleEngine::ai('gemini')->send($messages);
}
```

### Mixed Provider Usage

```php
// Use OpenAI for complex tasks
$complexResponse = BuppleEngine::ai('openai')
    ->withModel('gpt-4')
    ->send($complexMessages);

// Use Gemini for simpler tasks
$simpleResponse = BuppleEngine::ai('gemini')
    ->send($simpleMessages);
```

### Streaming with Error Handling

```php
return response()->stream(function () {
    try {
        $stream = BuppleEngine::ai('openai')->stream([
            ['role' => 'user', 'content' => 'Write a story...']
        ]);

        foreach ($stream as $chunk) {
            echo "data: " . json_encode(['content' => $chunk['content']]) . "\n\n";
            ob_flush();
            flush();
        }
    } catch (AiProviderException $e) {
        echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
    }
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]); 
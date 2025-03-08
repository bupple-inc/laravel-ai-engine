# AI Interface

The AI Interface provides methods for interacting with various AI providers (OpenAI, Google Gemini, and Anthropic Claude) in a unified way.

## Basic Usage

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get AI instance with default provider
$ai = BuppleEngine::ai();

// Get AI instance with specific provider
$ai = BuppleEngine::ai('openai'); // or 'gemini', 'claude'
```

## Available Methods

### send()

Send a chat completion request to the AI provider.

```php
public function send(array $messages): array
```

#### Parameters:
- `messages` (array): Array of message objects with 'role' and 'content'

#### Returns:
- `array`: Response containing the AI's reply and metadata

#### Example:
```php
$response = $ai->send([
    [
        'role' => 'system',
        'content' => 'You are a helpful assistant.'
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?'
    ]
]);

// Response structure
[
    'content' => string,    // The AI's response text
    'role' => 'assistant',  // Always 'assistant' for AI responses
    'model' => string,      // The model used (e.g., 'gpt-4')
    'provider' => string,   // The provider used (e.g., 'openai')
    'usage' => [
        'prompt_tokens' => int,
        'completion_tokens' => int,
        'total_tokens' => int
    ]
]
```

### stream()

Stream a chat completion response in real-time.

```php
public function stream(array $messages): Generator
```

#### Parameters:
- `messages` (array): Array of message objects with 'role' and 'content'

#### Returns:
- `Generator`: Yields chunks of the response as they arrive

#### Example:
```php
$stream = $ai->stream([
    ['role' => 'user', 'content' => 'Write a story about...']
]);

foreach ($stream as $chunk) {
    // Each chunk has this structure:
    [
        'content' => string,    // Part of the response
        'role' => 'assistant',  // Always 'assistant'
        'done' => bool         // Whether this is the last chunk
    ]
}
```

### withModel()

Set the AI model to use for the request.

```php
public function withModel(string $model): self
```

#### Parameters:
- `model` (string): The model identifier

#### Returns:
- `self`: Returns the instance for method chaining

#### Available Models:
- OpenAI: `gpt-4`, `gpt-3.5-turbo`
- Gemini: `gemini-pro`
- Claude: `claude-3-opus-20240229`

#### Example:
```php
$response = $ai->withModel('gpt-4')->send([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

### withTemperature()

Set the temperature for response generation.

```php
public function withTemperature(float $temperature): self
```

#### Parameters:
- `temperature` (float): Value between 0 and 1 (0 = more focused, 1 = more creative)

#### Returns:
- `self`: Returns the instance for method chaining

#### Example:
```php
$response = $ai->withTemperature(0.7)->send([
    ['role' => 'user', 'content' => 'Generate a creative story']
]);
```

### withMaxTokens()

Set the maximum number of tokens for the response.

```php
public function withMaxTokens(int $maxTokens): self
```

#### Parameters:
- `maxTokens` (int): Maximum number of tokens to generate

#### Returns:
- `self`: Returns the instance for method chaining

#### Example:
```php
$response = $ai->withMaxTokens(1000)->send([
    ['role' => 'user', 'content' => 'Summarize this article...']
]);
```

## Error Handling

The AI Interface throws `AiProviderException` when there are issues with the AI provider:

```php
use Bupple\Engine\Exceptions\AiProviderException;

try {
    $response = $ai->send($messages);
} catch (AiProviderException $e) {
    // Handle provider-specific errors
    $errorMessage = $e->getMessage();
    $errorCode = $e->getCode();
    $provider = $e->getProvider();
}
```

## Best Practices

1. **Model Selection**:
   - Use `gpt-4` for complex tasks requiring high accuracy
   - Use `gpt-3.5-turbo` for simpler tasks and cost efficiency
   - Use `gemini-pro` for balanced performance and cost
   - Use `claude-3` for specialized tasks

2. **Temperature Settings**:
   - Use low temperature (0.1-0.3) for factual responses
   - Use medium temperature (0.4-0.7) for balanced responses
   - Use high temperature (0.8-1.0) for creative responses

3. **Token Management**:
   - Monitor token usage through the response's `usage` field
   - Set appropriate `maxTokens` to control response length
   - Consider cost implications of token usage

4. **Error Handling**:
   - Always implement proper error handling
   - Consider implementing retry logic for temporary failures
   - Log errors for monitoring and debugging

## Examples

### Basic Chat Completion
```php
$response = BuppleEngine::ai()
    ->withModel('gpt-4')
    ->withTemperature(0.7)
    ->send([
        ['role' => 'user', 'content' => 'Hello!']
    ]);
```

### Streaming with SSE
```php
return response()->stream(function () {
    $stream = BuppleEngine::ai()
        ->withModel('gpt-4')
        ->stream([
            ['role' => 'user', 'content' => 'Write a story...']
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

### Provider-Specific Usage
```php
// Using OpenAI
$openai = BuppleEngine::ai('openai')
    ->withModel('gpt-4')
    ->withTemperature(0.7);

// Using Gemini
$gemini = BuppleEngine::ai('gemini')
    ->withModel('gemini-pro')
    ->withTemperature(0.5);

// Using Claude
$claude = BuppleEngine::ai('claude')
    ->withModel('claude-3-opus-20240229')
    ->withTemperature(0.8);
``` 
# Basic Usage

This guide covers the fundamental operations of the Bupple Laravel AI Engine, demonstrating how to use its core features.

## Getting Started

```php
use Bupple\Engine\Facades\BuppleEngine;

// Get the default AI provider
$ai = BuppleEngine::ai();

// Get a specific provider
$openai = BuppleEngine::ai('openai');
$gemini = BuppleEngine::ai('gemini');
$claude = BuppleEngine::ai('claude');
```

## Simple Chat Completion

```php
// Send a simple message
$response = BuppleEngine::ai()->send([
    ['role' => 'user', 'content' => 'What is Laravel?']
]);

echo $response['content'];
```

## Using System Messages

```php
// Chat with system context
$response = BuppleEngine::ai()->send([
    [
        'role' => 'system',
        'content' => 'You are a Laravel expert providing concise answers.'
    ],
    [
        'role' => 'user',
        'content' => 'What is Laravel?'
    ]
]);
```

## Memory Management

```php
// Get memory manager
$memory = BuppleEngine::memory();

// Set conversation context
$memory->setParent('conversation', $conversationId);

// Add user message
$memory->addUserMessage('What is Laravel?');

// Get chat completion with history
$messages = $memory->getMessages();
$response = BuppleEngine::ai()->send($messages);

// Store assistant response
$memory->addAssistantMessage($response['content']);
```

## Streaming Responses

```php
// In your controller
public function stream(Request $request)
{
    return response()->stream(function () use ($request) {
        $stream = BuppleEngine::ai()->stream([
            ['role' => 'user', 'content' => $request->message]
        ]);

        foreach ($stream as $chunk) {
            echo "data: " . json_encode([
                'content' => $chunk['content']
            ]) . "\n\n";
            
            ob_flush();
            flush();
        }
    }, 200, [
        'Cache-Control' => 'no-cache',
        'Content-Type' => 'text/event-stream',
    ]);
}
```

## Error Handling

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
} catch (\Exception $e) {
    // Handle other errors
    Log::error('Unexpected Error', [
        'message' => $e->getMessage(),
    ]);
}
```

## Complete Example

Here's a complete example of a chat controller:

```php
use Bupple\Engine\Facades\BuppleEngine;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        try {
            // Initialize memory
            $memory = BuppleEngine::memory();
            
            // Set conversation context
            $memory->setParent('conversation', $request->conversation_id);
            
            // Add user message
            $memory->addUserMessage($request->message);
            
            // Get conversation history
            $messages = $memory->getMessages();
            
            // Get AI response
            $response = BuppleEngine::ai()->send($messages);
            
            // Store AI response
            $memory->addAssistantMessage($response['content']);
            
            return response()->json([
                'success' => true,
                'message' => $response['content']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function stream(Request $request)
    {
        return response()->stream(function () use ($request) {
            try {
                // Initialize memory
                $memory = BuppleEngine::memory();
                $memory->setParent('conversation', $request->conversation_id);
                
                // Add user message
                $memory->addUserMessage($request->message);
                
                // Get conversation history
                $messages = $memory->getMessages();
                
                // Stream AI response
                $fullResponse = '';
                $stream = BuppleEngine::ai()->stream($messages);
                
                foreach ($stream as $chunk) {
                    $fullResponse .= $chunk['content'];
                    
                    echo "data: " . json_encode([
                        'content' => $chunk['content'],
                        'done' => false
                    ]) . "\n\n";
                    
                    ob_flush();
                    flush();
                }
                
                // Store complete response
                $memory->addAssistantMessage($fullResponse);
                
                echo "data: " . json_encode([
                    'content' => '',
                    'done' => true
                ]) . "\n\n";
            } catch (\Exception $e) {
                echo "data: " . json_encode([
                    'error' => $e->getMessage(),
                    'done' => true
                ]) . "\n\n";
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
    }
}
```

## Next Steps

1. Learn about [AI Providers](core/ai-providers)
2. Explore [Memory Management](core/memory-management)
3. Read about [Streaming](core/streaming) 
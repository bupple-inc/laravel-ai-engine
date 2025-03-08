# Streaming

The Bupple Laravel AI Engine provides robust support for streaming AI responses using Server-Sent Events (SSE).

## Basic Streaming

The simplest way to stream AI responses:

```php
use Bupple\Engine\Facades\BuppleEngine;

return response()->stream(function () {
    $stream = BuppleEngine::ai()->stream([
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

## Stream Response Format

Each chunk in the stream contains:

```php
[
    'content' => string,    // The chunk of response text
    'role' => 'assistant',  // Always 'assistant' for AI responses
    'done' => bool         // Whether this is the last chunk
]
```

## Provider-Specific Streaming

### OpenAI Streaming
```php
$stream = BuppleEngine::ai('openai')
    ->withModel('gpt-4')
    ->withTemperature(0.7)
    ->stream([
        ['role' => 'user', 'content' => 'Write a story...']
    ]);
```

### Gemini Streaming
```php
$stream = BuppleEngine::ai('gemini')
    ->withModel('gemini-pro')
    ->stream([
        ['role' => 'user', 'content' => 'Write a story...']
    ]);
```

### Claude Streaming
```php
$stream = BuppleEngine::ai('claude')
    ->withModel('claude-3-opus-20240229')
    ->stream([
        ['role' => 'user', 'content' => 'Write a story...']
    ]);
```

## Error Handling

Implement proper error handling for streams:

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
                'content' => $chunk['content'],
                'done' => $chunk['done']
            ]) . "\n\n";

            ob_flush();
            flush();
        }
    } catch (\Exception $e) {
        echo "data: " . json_encode([
            'error' => $e->getMessage()
        ]) . "\n\n";
    }
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]);
```

## Client-Side Implementation

### JavaScript Example
```javascript
const eventSource = new EventSource('/api/stream');

eventSource.onmessage = (event) => {
    const data = JSON.parse(event.data);
    
    if (data.error) {
        console.error('Stream error:', data.error);
        eventSource.close();
        return;
    }

    // Handle the chunk
    console.log('Received chunk:', data.content);

    if (data.done) {
        eventSource.close();
    }
};

eventSource.onerror = (error) => {
    console.error('EventSource failed:', error);
    eventSource.close();
};
```

### Vue.js Example
```vue
<template>
  <div>
    <div v-html="streamedContent"></div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      streamedContent: '',
      eventSource: null
    }
  },
  methods: {
    startStreaming() {
      this.eventSource = new EventSource('/api/stream');
      
      this.eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        
        if (data.error) {
          console.error('Stream error:', data.error);
          this.eventSource.close();
          return;
        }

        this.streamedContent += data.content;

        if (data.done) {
          this.eventSource.close();
        }
      };
    }
  },
  beforeUnmount() {
    if (this.eventSource) {
      this.eventSource.close();
    }
  }
}
</script>
```

## Best Practices

### 1. Connection Management
- Always close EventSource when done
- Handle connection errors gracefully
- Check for aborted connections

### 2. Error Handling
- Implement proper error handling on both server and client
- Send structured error messages
- Log errors for debugging

### 3. Performance
- Use appropriate buffer sizes
- Implement timeout handling
- Consider implementing retry logic

### 4. Memory Management
- Clear buffers regularly
- Monitor memory usage
- Implement maximum streaming duration

## Advanced Usage

### With Memory Management
```php
use Bupple\Engine\Facades\BuppleEngine;

return response()->stream(function () {
    $memory = BuppleEngine::memory();
    $memory->setParent('conversation', $conversationId);
    
    // Add user message to memory
    $memory->addUserMessage('Write a story about...');
    
    // Get conversation history
    $messages = $memory->getMessages();
    
    // Stream response
    $stream = BuppleEngine::ai()->stream($messages);
    
    $fullResponse = '';
    foreach ($stream as $chunk) {
        $fullResponse .= $chunk['content'];
        echo "data: " . json_encode($chunk) . "\n\n";
        ob_flush();
        flush();
    }
    
    // Store complete response in memory
    $memory->addAssistantMessage($fullResponse);
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]);
```

### Custom Event Types
```php
return response()->stream(function () {
    $stream = BuppleEngine::ai()->stream([
        ['role' => 'user', 'content' => 'Write a story...']
    ]);

    foreach ($stream as $chunk) {
        if ($chunk['done']) {
            echo "event: completion\n";
        } else {
            echo "event: chunk\n";
        }

        echo "data: " . json_encode($chunk) . "\n\n";
        ob_flush();
        flush();
    }
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]);
```

### With Progress Updates
```php
return response()->stream(function () {
    echo "data: " . json_encode(['type' => 'status', 'message' => 'Starting...']) . "\n\n";
    
    $stream = BuppleEngine::ai()->stream([
        ['role' => 'user', 'content' => 'Write a story...']
    ]);

    $tokens = 0;
    foreach ($stream as $chunk) {
        $tokens += str_word_count($chunk['content']);
        
        echo "data: " . json_encode([
            'type' => 'chunk',
            'content' => $chunk['content'],
            'tokens' => $tokens
        ]) . "\n\n";
        
        ob_flush();
        flush();
    }
    
    echo "data: " . json_encode(['type' => 'status', 'message' => 'Complete']) . "\n\n";
}, 200, [
    'Cache-Control' => 'no-cache',
    'Content-Type' => 'text/event-stream',
]); 
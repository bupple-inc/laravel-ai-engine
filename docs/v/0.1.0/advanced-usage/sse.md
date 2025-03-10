# Advanced SSE Usage

This guide covers advanced features and techniques for using the Bupple AI Engine Server-Sent Events (SSE) capabilities.

## Custom Event Types

### Sending Different Event Types

```php
use Bupple\Engine\Facades\Engine;

public function stream()
{
    return response()->stream(function () {
        // Send start event
        Engine::sse()->send([
            'type' => 'start',
            'message' => 'Starting stream'
        ], 'start');

        // Send content chunks
        $stream = Engine::engine()->stream($messages);
        foreach ($stream as $chunk) {
            Engine::sse()->send($chunk, 'content');
        }

        // Send completion event
        Engine::sse()->send([
            'type' => 'complete',
            'message' => 'Stream completed'
        ], 'complete');
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

### Client-Side Event Handling

```javascript
const eventSource = new EventSource('/api/stream');

eventSource.addEventListener('start', (event) => {
    console.log('Stream started:', JSON.parse(event.data));
    // Initialize UI components
});

eventSource.addEventListener('content', (event) => {
    const data = JSON.parse(event.data);
    // Process content chunks
    appendContent(data.content);
});

eventSource.addEventListener('complete', (event) => {
    console.log('Stream completed:', JSON.parse(event.data));
    eventSource.close();
    // Finalize UI updates
});

eventSource.addEventListener('error', (event) => {
    console.error('Stream error:', event);
    eventSource.close();
    // Handle error in UI
});
```

## Progress Tracking

### Server-Side Progress Updates

```php
public function streamWithProgress()
{
    return response()->stream(function () {
        $totalChunks = 10;
        $processedChunks = 0;

        $stream = Engine::engine()->stream($messages);
        foreach ($stream as $chunk) {
            $processedChunks++;
            $progress = ($processedChunks / $totalChunks) * 100;

            // Send content
            Engine::sse()->send($chunk, 'content');

            // Send progress update
            Engine::sse()->send([
                'progress' => $progress,
                'processed' => $processedChunks,
                'total' => $totalChunks
            ], 'progress');
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

### Client-Side Progress Handling

```javascript
let progressBar = document.querySelector('.progress-bar');

eventSource.addEventListener('progress', (event) => {
    const data = JSON.parse(event.data);
    progressBar.style.width = `${data.progress}%`;
    progressBar.setAttribute('aria-valuenow', data.progress);
    
    // Update progress text
    document.querySelector('.progress-text').textContent = 
        `Processing: ${data.processed}/${data.total}`;
});
```

## Connection Management

### Server-Side Connection Handling

```php
public function streamWithConnectionManagement()
{
    return response()->stream(function () {
        // Set initial connection timeout
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        // Send keepalive every 30 seconds
        $lastKeepalive = time();
        $keepaliveInterval = 30;

        $stream = Engine::engine()->stream($messages);
        foreach ($stream as $chunk) {
            // Send content
            Engine::sse()->send($chunk, 'content');

            // Check if keepalive needed
            if (time() - $lastKeepalive >= $keepaliveInterval) {
                Engine::sse()->send(['type' => 'keepalive'], 'keepalive');
                $lastKeepalive = time();
            }

            // Check connection status
            if (connection_aborted()) {
                // Clean up and exit
                break;
            }
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

### Client-Side Connection Management

```javascript
class SSEManager {
    constructor(url, options = {}) {
        this.url = url;
        this.options = {
            reconnectAttempts: 3,
            reconnectDelay: 1000,
            ...options
        };
        this.attemptCount = 0;
        this.connect();
    }

    connect() {
        this.eventSource = new EventSource(this.url);
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.eventSource.addEventListener('open', () => {
            console.log('Connection established');
            this.attemptCount = 0;
        });

        this.eventSource.addEventListener('error', (error) => {
            console.error('Connection error:', error);
            this.handleError();
        });

        this.eventSource.addEventListener('keepalive', () => {
            console.log('Keepalive received');
        });
    }

    handleError() {
        if (this.attemptCount < this.options.reconnectAttempts) {
            this.attemptCount++;
            console.log(`Reconnecting... Attempt ${this.attemptCount}`);
            
            setTimeout(() => {
                this.eventSource.close();
                this.connect();
            }, this.options.reconnectDelay);
        } else {
            console.error('Max reconnection attempts reached');
            this.eventSource.close();
        }
    }

    close() {
        if (this.eventSource) {
            this.eventSource.close();
        }
    }
}

// Usage
const sseManager = new SSEManager('/api/stream', {
    reconnectAttempts: 5,
    reconnectDelay: 2000
});
```

## Advanced Error Handling

### Server-Side Error Handling

```php
public function streamWithErrorHandling()
{
    return response()->stream(function () {
        try {
            $stream = Engine::engine()->stream($messages);
            
            foreach ($stream as $chunk) {
                try {
                    // Process and send chunk
                    $processedChunk = $this->processChunk($chunk);
                    Engine::sse()->send($processedChunk, 'content');
                } catch (\Exception $e) {
                    // Handle chunk processing error
                    Engine::sse()->send([
                        'error' => 'Chunk processing failed',
                        'message' => $e->getMessage(),
                        'chunk_id' => $chunk['id'] ?? null
                    ], 'chunk_error');
                    
                    // Continue with next chunk
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Handle stream error
            Engine::sse()->send([
                'error' => 'Stream processing failed',
                'message' => $e->getMessage()
            ], 'stream_error');
            
            // Attempt recovery or graceful shutdown
            $this->handleStreamError($e);
        } finally {
            // Clean up resources
            $this->cleanup();
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'X-Accel-Buffering' => 'no',
    ]);
}
```

### Client-Side Error Handling

```javascript
class SSEErrorHandler {
    constructor(eventSource) {
        this.eventSource = eventSource;
        this.setupErrorHandlers();
    }

    setupErrorHandlers() {
        this.eventSource.addEventListener('chunk_error', (event) => {
            const error = JSON.parse(event.data);
            console.warn('Chunk processing error:', error);
            this.handleChunkError(error);
        });

        this.eventSource.addEventListener('stream_error', (event) => {
            const error = JSON.parse(event.data);
            console.error('Stream error:', error);
            this.handleStreamError(error);
        });

        this.eventSource.addEventListener('error', (event) => {
            console.error('Connection error:', event);
            this.handleConnectionError(event);
        });
    }

    handleChunkError(error) {
        // Update UI to show chunk error
        this.showError(`Chunk processing failed: ${error.message}`);
        
        // Optionally retry chunk
        if (error.chunk_id) {
            this.retryChunk(error.chunk_id);
        }
    }

    handleStreamError(error) {
        // Update UI to show stream error
        this.showError(`Stream failed: ${error.message}`);
        
        // Close connection and cleanup
        this.eventSource.close();
        this.cleanup();
    }

    handleConnectionError(event) {
        // Update UI to show connection error
        this.showError('Connection lost');
        
        // Attempt to reconnect
        this.reconnect();
    }

    showError(message) {
        // Update UI with error message
        const errorContainer = document.querySelector('.error-container');
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
    }

    cleanup() {
        // Perform cleanup tasks
    }

    reconnect() {
        // Implement reconnection logic
    }

    retryChunk(chunkId) {
        // Implement chunk retry logic
    }
}

// Usage
const eventSource = new EventSource('/api/stream');
const errorHandler = new SSEErrorHandler(eventSource);
```

## Next Steps

For more advanced topics, check out:
- [Advanced Engine Usage](./engine.md)
- [Advanced Memory Usage](./memory.md)
- [Error Handling](./error-handling.md) 
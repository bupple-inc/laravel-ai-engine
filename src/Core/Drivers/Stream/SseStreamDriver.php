<?php

namespace Bupple\Engine\Core\Drivers\Stream;

class SseStreamDriver
{
    /**
     * SSE retry timeout in milliseconds.
     */
    protected int $retryTimeout = 3000;

    /**
     * Event ID.
     */
    protected ?string $id = null;

    /**
     * Event type.
     */
    protected string $eventType = 'message';

    /**
     * Custom headers.
     */
    protected array $customHeaders = [];

    /**
     * Whether to flush after each message.
     */
    protected bool $autoFlush = true;

    /**
     * Start SSE stream with headers.
     */
    public function start(array $headers = []): void
    {
        if (ob_get_level()) {
            ob_end_clean();
        }

        $defaultHeaders = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
            'Connection' => 'keep-alive',
        ];

        $headers = array_merge($defaultHeaders, $headers, $this->customHeaders);

        foreach ($headers as $name => $value) {
            header("$name: $value");
        }

        // Send 2KB of initial padding for IE
        echo str_repeat(' ', 2048) . "\n";
        $this->flush();
    }

    /**
     * Send a message.
     */
    public function send(string|array $data, ?string $id = null, ?string $eventType = null, bool $flush = null): void
    {
        $id = $id ?? $this->id;
        $eventType = $eventType ?? $this->eventType;
        $flush = $flush ?? $this->autoFlush;

        if ($id) {
            echo "id: $id\n";
        }

        if ($eventType !== 'message') {
            echo "event: $eventType\n";
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        // Split message by lines and send each line separately
        foreach (explode("\n", $data) as $line) {
            echo "data: $line\n";
        }

        echo "\n";

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * Send multiple messages.
     */
    public function sendBatch(array $messages, bool $flush = null): void
    {
        foreach ($messages as $message) {
            $id = $message['id'] ?? null;
            $eventType = $message['event'] ?? null;
            $data = $message['data'] ?? '';

            // Don't flush until the last message
            $isLast = end($messages) === $message;
            $this->send($data, $id, $eventType, $isLast && ($flush ?? $this->autoFlush));
        }
    }

    /**
     * Send a comment to keep the connection alive.
     */
    public function keepAlive(string $comment = ''): void
    {
        echo ": " . ($comment ?: 'keepalive') . "\n\n";
        $this->flush();
    }

    /**
     * Send an error message.
     */
    public function sendError(string $message, int $code = 500, bool $flush = true): void
    {
        $this->send([
            'error' => true,
            'message' => $message,
            'code' => $code,
        ], null, 'error', $flush);
    }

    /**
     * End the SSE stream.
     */
    public function end(?string $data = null): void
    {
        if ($data !== null) {
            $this->send($data);
        }
        $this->send(['type' => 'done'], null, 'done');
        die();
    }

    /**
     * Set retry timeout.
     */
    public function setRetryTimeout(int $milliseconds): self
    {
        $this->retryTimeout = $milliseconds;
        echo "retry: $milliseconds\n\n";
        return $this;
    }

    /**
     * Set event ID.
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set event type.
     */
    public function setEventType(string $type): self
    {
        $this->eventType = $type;
        return $this;
    }

    /**
     * Set custom headers.
     */
    public function setHeaders(array $headers): self
    {
        $this->customHeaders = $headers;
        return $this;
    }

    /**
     * Set auto flush.
     */
    public function setAutoFlush(bool $autoFlush): self
    {
        $this->autoFlush = $autoFlush;
        return $this;
    }

    /**
     * Flush the output buffer.
     */
    protected function flush(): void
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            flush();
            if (ob_get_level() > 0) {
                ob_flush();
            }
        }
    }
}

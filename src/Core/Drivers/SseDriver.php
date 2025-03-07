<?php

namespace Bupple\Engine\Core\Drivers;

class SseDriver
{
    public function __construct()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
    }

    public function send($data, $event = 'message')
    {
        echo "event: {$event}\n";
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();
    }

    public function close()
    {
        echo "event: close\n";
        echo "data: [DONE]\n\n";
        ob_flush();
        flush();
    }
}

<?php

namespace BuppleEngine\Core\Drivers\Engine;

use BuppleEngine\Core\Drivers\Contracts\ChatDriverInterface;
use BuppleEngine\Core\Memory\ClaudeMemoryDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ClaudeDriver extends AbstractEngineDriver
{
    /**
     * The configuration array.
     */
    protected array $config;

    /**
     * The HTTP client instance.
     */
    protected Client $client;

    /**
     * Create a new Claude driver instance.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
            'headers' => [
                'x-api-key' => $config['api_key'] ?? env('CLAUDE_API_KEY'),
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Send a message to Claude and get a response.
     */
    public function send(array $messages): array
    {
        try {
            // Convert messages array to Claude format
            $formattedMessages = $this->formatMessages($messages);

            $response = $this->client->post('messages', [
                'json' => [
                    'model' => $this->config['model'] ?? env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
                    'messages' => $formattedMessages,
                    'temperature' => (float) ($this->config['temperature'] ?? env('CLAUDE_TEMPERATURE', 0.7)),
                    'max_tokens' => (int) ($this->config['max_tokens'] ?? env('CLAUDE_MAX_TOKENS', 1000)),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return [
                'role' => 'assistant',
                'content' => $result['content'][0]['text'],
                'model' => $result['model'],
                'usage' => $result['usage'] ?? null,
            ];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude API request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Stream a chat completion from Claude.
     */
    public function stream(array $messages): \Generator
    {
        try {
            $formattedMessages = $this->formatMessages($messages);

            $response = $this->client->post('messages', [
                'json' => [
                    'model' => $this->config['model'] ?? env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
                    'messages' => $formattedMessages,
                    'temperature' => (float) ($this->config['temperature'] ?? env('CLAUDE_TEMPERATURE', 0.7)),
                    'max_tokens' => (int) ($this->config['max_tokens'] ?? env('CLAUDE_MAX_TOKENS', 1000)),
                    'stream' => true,
                ],
                'stream' => true,
                'read_timeout' => 0,
                'http_errors' => true,
                'headers' => [
                    'Accept' => 'text/event-stream',
                    'Cache-Control' => 'no-cache',
                    'Connection' => 'keep-alive',
                ],
                'decode_content' => true,
                'verify' => false
            ]);

            $stream = $response->getBody()->detach();
            stream_set_blocking($stream, false);

            while (!feof($stream)) {
                $line = fgets($stream);
                if (!empty($line)) {
                    $data = json_decode($line, true);
                    if ($data && isset($data['type']) && $data['type'] === 'content_block_delta') {
                        yield [
                            'content' => $data['delta']['text'],
                            'model' => $data['model'] ?? null,
                        ];
                        if (function_exists('fastcgi_finish_request')) {
                            fastcgi_finish_request();
                        } else {
                            flush();
                            ob_flush();
                        }
                    }
                }
            }
            fclose($stream);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude streaming request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the driver's configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Format messages for Claude API.
     */
    protected function formatMessages(array $messages): array
    {
        return array_map(function ($message) {
            // Claude uses 'user' and 'assistant' roles
            $role = match ($message['role']) {
                'system' => 'user',
                default => $message['role'],
            };

            return [
                'role' => $role,
                'content' => $message['content'],
            ];
        }, $messages);
    }

    protected function getBaseUri(): string
    {
        return 'https://api.anthropic.com/v1/';
    }

    protected function getHeaders(): array
    {
        return [
            'x-api-key' => $this->config['api_key'] ?? env('CLAUDE_API_KEY'),
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ];
    }

    protected function formatOptions(array $options): array
    {
        return [
            'model' => $options['model'] ?? env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
            'temperature' => (float) ($options['temperature'] ?? env('CLAUDE_TEMPERATURE', 0.7)),
            'max_tokens' => (int) ($options['max_tokens'] ?? env('CLAUDE_MAX_TOKENS', 1000)),
        ];
    }
}

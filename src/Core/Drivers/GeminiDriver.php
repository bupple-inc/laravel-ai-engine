<?php

namespace BuppleEngine\Core\Drivers;

use BuppleEngine\Core\Drivers\Contracts\ChatDriverInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeminiDriver implements ChatDriverInterface
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
     * Create a new Gemini driver instance.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $apiKey = $config['api_key'] ?? env('GEMINI_API_KEY');
        $projectId = $config['project_id'] ?? env('GEMINI_PROJECT_ID');

        $baseUri = $projectId
            ? "https://generativelanguage.googleapis.com/v1/projects/{$projectId}/"
            : 'https://generativelanguage.googleapis.com/v1/';

        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers' => [
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Send a message to Gemini and get a response.
     */
    public function send(array $messages): array
    {
        try {
            // Convert messages array to Gemini format
            $formattedMessages = $this->formatMessages($messages);

            $response = $this->client->post('models/' . ($this->config['model'] ?? env('GEMINI_MODEL', 'gemini-pro')) . ':generateContent', [
                'json' => [
                    'contents' => $formattedMessages,
                    'generationConfig' => [
                        'temperature' => (float) ($this->config['temperature'] ?? env('GEMINI_TEMPERATURE', 0.7)),
                        'maxOutputTokens' => (int) ($this->config['max_tokens'] ?? env('GEMINI_MAX_TOKENS', 1000)),
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return [
                'role' => 'assistant',
                'content' => $result['candidates'][0]['content']['parts'][0]['text'],
                'model' => $this->config['model'] ?? env('GEMINI_MODEL', 'gemini-pro'),
                'usage' => $result['usageMetadata'] ?? null,
            ];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Gemini API request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Stream a chat completion from Gemini.
     */
    public function stream(array $messages): \Generator
    {
        try {
            $formattedMessages = $this->formatMessages($messages);

            $response = $this->client->post('models/' . ($this->config['model'] ?? env('GEMINI_MODEL', 'gemini-pro')) . ':streamGenerateContent', [
                'json' => [
                    'contents' => $formattedMessages,
                    'generationConfig' => [
                        'temperature' => (float) ($this->config['temperature'] ?? env('GEMINI_TEMPERATURE', 0.7)),
                        'maxOutputTokens' => (int) ($this->config['max_tokens'] ?? env('GEMINI_MAX_TOKENS', 1000)),
                    ],
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
                    if ($data && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        yield [
                            'content' => $data['candidates'][0]['content']['parts'][0]['text'],
                            'model' => $this->config['model'] ?? env('GEMINI_MODEL', 'gemini-pro'),
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
            throw new \RuntimeException('Gemini streaming request failed: ' . $e->getMessage(), $e->getCode(), $e);
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
     * Format messages for Gemini API.
     */
    protected function formatMessages(array $messages): array
    {
        $formattedMessages = [];
        foreach ($messages as $message) {
            $formattedMessages[] = [
                'role' => $this->mapRole($message['role']),
                'parts' => [
                    ['text' => $message['content']],
                ],
            ];
        }
        return $formattedMessages;
    }

    /**
     * Map standard roles to Gemini roles.
     */
    protected function mapRole(string $role): string
    {
        return match ($role) {
            'system', 'assistant' => 'model',
            'user' => 'user',
            default => 'user',
        };
    }
}

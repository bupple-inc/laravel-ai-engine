<?php

namespace BuppleEngine\Core\Drivers\Engine\Gemini;

use BuppleEngine\Core\Drivers\Engine\AbstractEngineDriver;
use BuppleEngine\Core\Drivers\Engine\Gemini\GeminiMemoryDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeminiDriver extends AbstractEngineDriver
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
     * Get the memory driver instance for this engine.
     */
    public function getMemoryDriver(mixed $parentModel): GeminiMemoryDriver
    {
        return new GeminiMemoryDriver($parentModel);
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

    /**
     * Get the base URI for the API.
     */
    protected function getBaseUri(): string
    {
        $projectId = $this->config['project_id'] ?? env('GEMINI_PROJECT_ID');
        return $projectId
            ? "https://generativelanguage.googleapis.com/v1/projects/{$projectId}/"
            : 'https://generativelanguage.googleapis.com/v1/';
    }

    /**
     * Get the headers for the API request.
     */
    protected function getHeaders(): array
    {
        return [
            'x-goog-api-key' => $this->config['api_key'] ?? env('GEMINI_API_KEY'),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Format options for the API request.
     */
    protected function formatOptions(array $options): array
    {
        return [
            'model' => $options['model'] ?? env('GEMINI_MODEL', 'gemini-pro'),
            'temperature' => (float) ($options['temperature'] ?? env('GEMINI_TEMPERATURE', 0.7)),
            'maxOutputTokens' => (int) ($options['max_tokens'] ?? env('GEMINI_MAX_TOKENS', 1000)),
        ];
    }
}

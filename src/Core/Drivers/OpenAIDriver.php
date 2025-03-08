<?php

namespace BuppleEngine\Core\Drivers;

use BuppleEngine\Core\Drivers\Contracts\ChatDriverInterface;
use BuppleEngine\Core\Memory\OpenAIMemoryDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIDriver implements ChatDriverInterface
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
     * Create a new OpenAI driver instance.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . ($config['api_key'] ?? env('OPENAI_API_KEY')),
                'Content-Type' => 'application/json',
            ],
        ]);

        if (isset($config['organization_id']) || env('OPENAI_ORGANIZATION_ID')) {
            $this->client = new Client([
                'base_uri' => 'https://api.openai.com/v1/',
                'headers' => [
                    'Authorization' => 'Bearer ' . ($config['api_key'] ?? env('OPENAI_API_KEY')),
                    'OpenAI-Organization' => $config['organization_id'] ?? env('OPENAI_ORGANIZATION_ID'),
                    'Content-Type' => 'application/json',
                ],
            ]);
        }
    }

    /**
     * Send a message to OpenAI and get a response.
     */
    public function send(array $messages): array
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->config['model'] ?? env('OPENAI_MODEL', 'gpt-4'),
                    'messages' => $messages,
                    'temperature' => (float) ($this->config['temperature'] ?? env('OPENAI_TEMPERATURE', 0.7)),
                    'max_tokens' => (int) ($this->config['max_tokens'] ?? env('OPENAI_MAX_TOKENS', 1000)),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return [
                'role' => $result['choices'][0]['message']['role'],
                'content' => $result['choices'][0]['message']['content'],
                'model' => $result['model'],
                'usage' => $result['usage'] ?? null,
            ];
        } catch (GuzzleException $e) {
            throw new \RuntimeException('OpenAI API request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Stream a chat completion from OpenAI.
     */
    public function stream(array $messages): \Generator
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->config['model'] ?? env('OPENAI_MODEL', 'gpt-4'),
                    'messages' => $messages,
                    'temperature' => (float) ($this->config['temperature'] ?? env('OPENAI_TEMPERATURE', 0.7)),
                    'max_tokens' => (int) ($this->config['max_tokens'] ?? env('OPENAI_MAX_TOKENS', 1000)),
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
                    $line = str_replace('data: ', '', trim($line));
                    if ($line === '[DONE]') {
                        break;
                    }

                    $data = json_decode($line, true);
                    if ($data && isset($data['choices'][0]['delta']['content'])) {
                        yield [
                            'content' => $data['choices'][0]['delta']['content'],
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
            throw new \RuntimeException('OpenAI streaming request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the driver's configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getMemoryDriver(mixed $parentModel): OpenAIMemoryDriver
    {
        return new OpenAIMemoryDriver($parentModel);
    }

    protected function formatMessages(array $messages): array
    {
        return array_map(function ($message) {
            return [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        }, $messages);
    }

    protected function formatOptions(array $options): array
    {
        $validOptions = [
            'temperature',
            'top_p',
            'n',
            'stop',
            'max_tokens',
            'presence_penalty',
            'frequency_penalty',
            'logit_bias',
            'user',
        ];

        return array_intersect_key($options, array_flip($validOptions));
    }
}

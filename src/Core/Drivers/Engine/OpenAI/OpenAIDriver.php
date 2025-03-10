<?php

namespace Bupple\Engine\Core\Drivers\Engine\OpenAI;

use Bupple\Engine\Core\Drivers\Engine\AbstractEngineDriver;
use Bupple\Engine\Core\Drivers\Engine\OpenAI\OpenAIMemoryDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIDriver extends AbstractEngineDriver
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
        parent::__construct($config);
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

    /**
     * Get the memory driver instance for this engine.
     */
    public function getMemoryDriver(mixed $parentModel): OpenAIMemoryDriver
    {
        return new OpenAIMemoryDriver($parentModel);
    }

    /**
     * Format messages for the API request.
     */
    protected function formatMessages(array $messages): array
    {
        return array_map(function ($message) {
            return [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        }, $messages);
    }

    /**
     * Format options for the API request.
     */
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

    /**
     * Get the base URI for the API.
     */
    protected function getBaseUri(): string
    {
        return 'https://api.openai.com/v1/';
    }

    /**
     * Get the headers for the API request.
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . ($this->config['api_key'] ?? env('OPENAI_API_KEY')),
            'Content-Type' => 'application/json',
        ];

        if (isset($this->config['organization_id']) || env('OPENAI_ORGANIZATION_ID')) {
            $headers['OpenAI-Organization'] = $this->config['organization_id'] ?? env('OPENAI_ORGANIZATION_ID');
        }

        return $headers;
    }
}

<?php

namespace Bupple\Engine\Core\Drivers;

use Bupple\Engine\Core\Memory\ClaudeMemoryDriver;
use GuzzleHttp\Exception\GuzzleException;

class ClaudeDriver extends AbstractChatDriver
{
    protected function getBaseUri(): string
    {
        return 'https://api.anthropic.com/v1/';
    }

    protected function getHeaders(): array
    {
        return [
            'x-api-key' => $this->config['api_key'],
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ];
    }

    public function chat(array $messages, array $options = []): array
    {
        try {
            $response = $this->client->post('messages', [
                'json' => [
                    'messages' => $this->formatMessages($messages),
                    'model' => $options['model'] ?? $this->config['model'] ?? 'claude-3-opus-20240229',
                    ...$this->formatOptions($options),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude API error: ' . $e->getMessage());
        }
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        try {
            $response = $this->client->post('messages', [
                'json' => [
                    'messages' => $this->formatMessages($messages),
                    'model' => $options['model'] ?? $this->config['model'] ?? 'claude-3-opus-20240229',
                    'stream' => true,
                    ...$this->formatOptions($options),
                ],
                'stream' => true,
            ]);

            $stream = $response->getBody();
            while (!$stream->eof()) {
                $line = trim($stream->read(1024));
                if (str_starts_with($line, 'data: ')) {
                    $data = substr($line, 6);
                    if ($data === '[DONE]') {
                        return;
                    }
                    $decoded = json_decode($data, true);
                    if ($decoded && isset($decoded['delta']['text'])) {
                        yield ['content' => $decoded['delta']['text']];
                    }
                }
            }
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Claude API error: ' . $e->getMessage());
        }
    }

    public function getMemoryDriver(mixed $parentModel): ClaudeMemoryDriver
    {
        return new ClaudeMemoryDriver($parentModel);
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
            'top_k',
            'max_tokens',
            'stop',
            'system',
            'metadata',
        ];

        $formattedOptions = array_intersect_key($options, array_flip($validOptions));

        // Rename options to match Claude's API
        if (isset($options['presence_penalty'])) {
            $formattedOptions['presence_penalty'] = $options['presence_penalty'];
        }
        if (isset($options['frequency_penalty'])) {
            $formattedOptions['frequency_penalty'] = $options['frequency_penalty'];
        }

        return $formattedOptions;
    }
}

<?php

namespace Bupple\Engine\Core\Drivers;

use Bupple\Engine\Core\Memory\OpenAIMemoryDriver;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIDriver extends AbstractChatDriver
{
    protected function getBaseUri(): string
    {
        return 'https://api.openai.com/v1/';
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->config['api_key'],
            'Content-Type' => 'application/json',
        ];
    }

    public function chat(array $messages, array $options = []): array
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'messages' => $this->formatMessages($messages),
                    'model' => $options['model'] ?? $this->config['model'] ?? 'gpt-4',
                    ...$this->formatOptions($options),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('OpenAI API error: ' . $e->getMessage());
        }
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'messages' => $this->formatMessages($messages),
                    'model' => $options['model'] ?? $this->config['model'] ?? 'gpt-4',
                    'stream' => true,
                    ...$this->formatOptions($options),
                ],
                'stream' => true,
            ]);

            $buffer = '';
            $stream = $response->getBody();
            while (!$stream->eof()) {
                $line = $stream->read(1);
                $buffer .= $line;

                if (str_ends_with($buffer, "\n")) {
                    $events = explode("\n", $buffer);
                    foreach ($events as $event) {
                        if (str_starts_with($event, 'data: ')) {
                            $data = substr($event, 6);
                            if ($data === '[DONE]') {
                                return;
                            }
                            $decoded = json_decode($data, true);
                            if ($decoded && isset($decoded['choices'][0]['delta'])) {
                                yield $decoded['choices'][0]['delta'];
                            }
                        }
                    }
                    $buffer = '';
                }
            }
        } catch (GuzzleException $e) {
            throw new \RuntimeException('OpenAI API error: ' . $e->getMessage());
        }
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

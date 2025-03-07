<?php

namespace Bupple\Engine\Core\Drivers;

use Bupple\Engine\Core\Memory\GeminiMemoryDriver;
use GuzzleHttp\Exception\GuzzleException;

class GeminiDriver extends AbstractChatDriver
{
    protected function getBaseUri(): string
    {
        return 'https://generativelanguage.googleapis.com/v1beta/';
    }

    protected function getHeaders(): array
    {
        return [
            'x-goog-api-key' => $this->config['api_key'],
            'Content-Type' => 'application/json',
        ];
    }

    public function chat(array $messages, array $options = []): array
    {
        try {
            $model = $options['model'] ?? $this->config['model'] ?? 'gemini-pro';
            $response = $this->client->post("models/{$model}:generateContent", [
                'json' => [
                    'contents' => $this->formatMessages($messages),
                    'generationConfig' => $this->formatOptions($options),
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Gemini API error: ' . $e->getMessage());
        }
    }

    public function stream(array $messages, array $options = []): \Generator
    {
        try {
            $model = $options['model'] ?? $this->config['model'] ?? 'gemini-pro';
            $response = $this->client->post("models/{$model}:streamGenerateContent", [
                'json' => [
                    'contents' => $this->formatMessages($messages),
                    'generationConfig' => $this->formatOptions($options),
                ],
                'stream' => true,
            ]);

            $stream = $response->getBody();
            while (!$stream->eof()) {
                $chunk = $stream->read(1024);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = json_decode($chunk, true);
                    if ($data && isset($data['candidates'][0]['content'])) {
                        yield $data['candidates'][0]['content'];
                    }
                }
            }
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Gemini API error: ' . $e->getMessage());
        }
    }

    public function getMemoryDriver(mixed $parentModel): GeminiMemoryDriver
    {
        return new GeminiMemoryDriver($parentModel);
    }

    protected function formatMessages(array $messages): array
    {
        return array_map(function ($message) {
            $role = $message['role'] === 'assistant' ? 'model' : $message['role'];
            return [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']],
                ],
            ];
        }, $messages);
    }

    protected function formatOptions(array $options): array
    {
        $validOptions = [
            'temperature',
            'topP',
            'topK',
            'maxOutputTokens',
            'stopSequences',
            'candidateCount',
        ];

        $formattedOptions = array_intersect_key($options, array_flip($validOptions));

        // Rename options to match Gemini's API
        if (isset($options['max_tokens'])) {
            $formattedOptions['maxOutputTokens'] = $options['max_tokens'];
        }
        if (isset($options['top_p'])) {
            $formattedOptions['topP'] = $options['top_p'];
        }
        if (isset($options['stop'])) {
            $formattedOptions['stopSequences'] = (array) $options['stop'];
        }

        return $formattedOptions;
    }
}

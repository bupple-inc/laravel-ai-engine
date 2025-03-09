<?php

namespace BuppleEngine\Core\Drivers\Engine\OpenAI;

use BuppleEngine\Core\Models\Memory;
use BuppleEngine\Core\Drivers\Engine\Memory\AbstractMemoryDriver;

class OpenAIMemoryDriver extends AbstractMemoryDriver
{
    /**
     * The driver configuration.
     *
     * @var array
     */
    protected array $config;

    /**
     * Create a new OpenAI memory driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Format the role for OpenAI.
     *
     * @param string $role
     * @return string
     */
    protected function formatRole(string $role): string
    {
        // OpenAI uses standard roles: system, user, assistant
        return $role;
    }

    /**
     * Format a message for OpenAI.
     *
     * @param Memory $message
     * @return array
     */
    protected function formatMessage(Memory $message): array
    {
        $formatted = [
            'role' => $message->role,
            'content' => $message->content,
            'type' => $message->type,
            'metadata' => $message->metadata,
        ];

        if ($message->type !== 'text') {
            $formatted['content'] = $this->handleMediaContent($formatted);
        }

        return [
            'role' => $formatted['role'],
            'content' => $formatted['content'],
        ];
    }

    /**
     * Handle media content formatting for OpenAI.
     *
     * @param array $message
     * @return array|string
     */
    private function handleMediaContent(array $message): array|string
    {
        if (isset($message['metadata']['description'])) {
            return $message['metadata']['description'];
        }

        $content = [];

        switch ($message['type']) {
            case 'image':
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => $message['content'],
                        'detail' => $message['metadata']['detail'] ?? 'auto',
                    ],
                ];
                break;
            case 'audio':
                $content[] = [
                    'type' => 'audio',
                    'audio' => [
                        'url' => $message['content'],
                        'format' => $message['metadata']['format'] ?? null,
                    ],
                ];
                break;
        }

        return $content;
    }

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    protected function getDriverName(): string
    {
        return 'openai';
    }

    /**
     * Get the configuration for this driver.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}

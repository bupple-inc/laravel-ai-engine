<?php

namespace BuppleEngine\Core\Drivers\Engine\Memory;

use BuppleEngine\Core\Models\Memory;
use BuppleEngine\Core\Drivers\Memory\AbstractMemoryDriver;

class ClaudeMemoryDriver extends AbstractMemoryDriver
{
    /**
     * The driver configuration.
     *
     * @var array
     */
    protected array $config;

    /**
     * Create a new Claude memory driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Format the role for Claude.
     *
     * @param string $role
     * @return string
     */
    protected function formatRole(string $role): string
    {
        // Claude uses standard roles: system, user, assistant
        return $role;
    }

    /**
     * Format a message for Claude.
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
     * Handle media content formatting for Claude.
     *
     * @param array $message
     * @return string
     */
    private function handleMediaContent(array $message): string
    {
        if (isset($message['metadata']['description'])) {
            return $message['metadata']['description'];
        }

        $content = '';

        switch ($message['type']) {
            case 'image':
                $content .= "<image>{$message['content']}</image>\n";
                if (isset($message['metadata']['caption'])) {
                    $content .= $message['metadata']['caption'] . "\n";
                }
                break;
            case 'audio':
                if (isset($message['metadata']['format'])) {
                    $content .= "<audio format=\"{$message['metadata']['format']}\">{$message['content']}</audio>\n";
                }
                if (isset($message['metadata']['transcript'])) {
                    $content .= $message['metadata']['transcript'] . "\n";
                }
                break;
        }

        return trim($content);
    }

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    protected function getDriverName(): string
    {
        return 'claude';
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

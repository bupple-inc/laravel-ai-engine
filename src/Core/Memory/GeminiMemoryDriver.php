<?php

namespace BuppleEngine\Core\Memory;

use BuppleEngine\Core\Models\Memory;

class GeminiMemoryDriver extends AbstractMemoryDriver
{
    /**
     * The driver configuration.
     *
     * @var array
     */
    protected array $config;

    /**
     * Create a new Gemini memory driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Store a memory using Gemini embeddings.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function store(string $key, mixed $value): bool
    {
        // Implementation will use Gemini embeddings to store memory
        // This is a placeholder for the actual implementation
        return true;
    }

    /**
     * Retrieve a memory using Gemini embeddings.
     *
     * @param string $key
     * @return mixed
     */
    public function retrieve(string $key): mixed
    {
        // Implementation will use Gemini embeddings to retrieve memory
        // This is a placeholder for the actual implementation
        return null;
    }

    /**
     * Check if a memory exists.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        // Implementation will check if memory exists
        // This is a placeholder for the actual implementation
        return false;
    }

    /**
     * Delete a memory.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        // Implementation will delete memory
        // This is a placeholder for the actual implementation
        return true;
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

    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        // Gemini uses 'model' instead of 'assistant' for responses
        $role = $role === 'assistant' ? 'model' : $role;

        Memory::create([
            'parent_class' => $this->parentClass,
            'parent_id' => $this->parentId,
            'message_id' => $messageId,
            'role' => $role,
            'content' => $content,
            'type' => $type,
            'metadata' => $metadata,
            'driver' => 'gemini',
        ]);
    }

    public function getMessages(): array
    {
        return Memory::where('parent_class', $this->parentClass)
            ->where('parent_id', $this->parentId)
            ->where('driver', 'gemini')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                $formattedMessage = $this->formatMediaContent([
                    'role' => $message->role === 'model' ? 'assistant' : $message->role,
                    'content' => $message->content,
                    'type' => $message->type,
                    'metadata' => $message->metadata,
                ]);

                // Gemini requires parts array for content
                if ($message->type === 'text') {
                    $formattedMessage['parts'] = [['text' => $formattedMessage['content']]];
                    unset($formattedMessage['content']);
                }

                return $formattedMessage;
            })
            ->toArray();
    }

    public function clear(): void
    {
        Memory::where('parent_class', $this->parentClass)
            ->where('parent_id', $this->parentId)
            ->where('driver', 'gemini')
            ->delete();
    }

    /**
     * Format the role for Gemini.
     *
     * @param string $role
     * @return string
     */
    protected function formatRole(string $role): string
    {
        // Gemini uses 'model' instead of 'assistant'
        return $role === 'assistant' ? 'model' : $role;
    }

    /**
     * Format a message for Gemini.
     *
     * @param Memory $message
     * @return array
     */
    protected function formatMessage(Memory $message): array
    {
        $formatted = [
            'role' => $message->role === 'model' ? 'assistant' : $message->role,
            'content' => $message->content,
            'type' => $message->type,
            'metadata' => $message->metadata,
        ];

        if ($message->type === 'text') {
            return [
                'role' => $formatted['role'],
                'parts' => [['text' => $formatted['content']]],
            ];
        }

        return [
            'role' => $formatted['role'],
            'parts' => $this->handleMediaContent($formatted),
        ];
    }

    /**
     * Handle media content formatting for Gemini.
     *
     * @param array $message
     * @return array
     */
    private function handleMediaContent(array $message): array
    {
        $parts = [];

        if (isset($message['metadata']['description'])) {
            $parts[] = ['text' => $message['metadata']['description']];
            return $parts;
        }

        switch ($message['type']) {
            case 'image':
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $message['metadata']['mime_type'] ?? 'image/jpeg',
                        'data' => $message['content'],
                    ],
                ];
                break;
            case 'audio':
                // Gemini currently doesn't support audio directly
                if (isset($message['metadata']['transcript'])) {
                    $parts[] = ['text' => $message['metadata']['transcript']];
                }
                break;
        }

        return $parts;
    }

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    protected function getDriverName(): string
    {
        return 'gemini';
    }
}

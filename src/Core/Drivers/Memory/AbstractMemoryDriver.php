<?php

namespace BuppleEngine\Core\Drivers\Memory;

use BuppleEngine\Core\Models\Memory;
use BuppleEngine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;

/**
 * Abstract Memory Driver
 * 
 * This abstract class provides a base implementation for memory drivers in the Bupple Engine.
 * It handles message management, content formatting, and basic memory operations.
 * 
 * @package BuppleEngine\Core\Drivers\Memory
 */
abstract class AbstractMemoryDriver implements MemoryDriverInterface
{
    /**
     * The driver configuration array containing settings for the memory driver.
     *
     * @var array
     */
    protected array $config;

    /**
     * The fully qualified class name of the parent entity for context.
     *
     * @var string|null
     */
    protected ?string $parentClass = null;

    /**
     * The unique identifier of the parent entity for context.
     *
     * @var string|int|null
     */
    protected string|int|null $parentId = null;

    /**
     * Create a new memory driver instance.
     *
     * @param array $config Configuration array for the driver
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    // region Abstract Methods

    /**
     * Format the role for the specific driver implementation.
     *
     * @param string $role The role to format
     * @return string The formatted role
     */
    abstract protected function formatRole(string $role): string;

    /**
     * Format a message for the specific driver implementation.
     *
     * @param Memory $message The message to format
     * @return array The formatted message
     */
    abstract protected function formatMessage(Memory $message): array;

    /**
     * Get the unique identifier for this driver implementation.
     *
     * @return string The driver name
     */
    abstract protected function getDriverName(): string;

    // endregion

    // region Context Management

    /**
     * Set the parent context for the memory operations.
     *
     * @param string $class The parent class name
     * @param string|int $id The parent identifier
     * @return void
     */
    public function setParent(string $class, string|int $id): void
    {
        $this->parentClass = $class;
        $this->parentId = $id;
    }

    /**
     * Get the configuration for this driver instance.
     *
     * @return array The driver configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    // endregion

    // region Message Management

    /**
     * Add a message to the chat history.
     *
     * @param string $role The role of the message sender
     * @param string $content The message content
     * @param string|null $type The message type (default: 'text')
     * @param array $metadata Additional metadata for the message
     * @param string|null $messageId Optional unique identifier for the message
     * @throws \RuntimeException When parent context is not set
     */
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        if ($this->parentClass === null || $this->parentId === null) {
            throw new \RuntimeException('Parent context must be set before adding messages.');
        }

        Memory::create([
            'parent_class' => $this->parentClass,
            'parent_id' => $this->parentId,
            'message_id' => $messageId,
            'role' => $this->formatRole($role),
            'content' => $content,
            'type' => $type,
            'metadata' => $metadata,
            'driver' => $this->getDriverName(),
        ]);
    }

    /**
     * Add a user message to the chat history.
     *
     * @param string $content The message content
     * @param string|null $type The message type
     * @param array $metadata Additional metadata
     * @param string|null $messageId Optional message identifier
     */
    public function addUserMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        $this->addMessage('user', $content, $type, $metadata, $messageId);
    }

    /**
     * Add an assistant message to the chat history.
     *
     * @param string $content The message content
     * @param string|null $type The message type
     * @param array $metadata Additional metadata
     * @param string|null $messageId Optional message identifier
     */
    public function addAssistantMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        $this->addMessage('assistant', $content, $type, $metadata, $messageId);
    }

    /**
     * Add a system message to the chat history.
     *
     * @param string $content The message content
     * @param string|null $type The message type
     * @param array $metadata Additional metadata
     * @param string|null $messageId Optional message identifier
     */
    public function addSystemMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        $this->addMessage('system', $content, $type, $metadata, $messageId);
    }

    /**
     * Retrieve all messages from the chat history.
     *
     * @return array Array of formatted messages
     * @throws \RuntimeException When parent context is not set
     */
    public function getMessages(): array
    {
        if ($this->parentClass === null || $this->parentId === null) {
            throw new \RuntimeException('Parent context must be set before retrieving messages.');
        }

        return Memory::where('parent_class', $this->parentClass)
            ->where('parent_id', $this->parentId)
            ->where('driver', $this->getDriverName())
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($message) => $this->formatMessage($message))
            ->toArray();
    }

    /**
     * Clear all messages from the chat history.
     *
     * @throws \RuntimeException When parent context is not set
     */
    public function clear(): void
    {
        if ($this->parentClass === null || $this->parentId === null) {
            throw new \RuntimeException('Parent context must be set before clearing messages.');
        }

        Memory::where('parent_class', $this->parentClass)
            ->where('parent_id', $this->parentId)
            ->where('driver', $this->getDriverName())
            ->delete();
    }

    // endregion

    // region Content Formatting

    /**
     * Format media content for storage or retrieval.
     *
     * @param array $message The message to format
     * @return array The formatted message
     */
    protected function formatMediaContent(array $message): array
    {
        if ($message['type'] === 'text') {
            return $message;
        }

        $content = [];
        if (isset($message['content'])) {
            foreach ((array) $message['content'] as $part) {
                if (is_string($part)) {
                    $content[] = ['type' => 'text', 'text' => $part];
                } else {
                    $content[] = $part;
                }
            }
        }

        $message['content'] = $content;
        return $message;
    }

    /**
     * Format image content for storage or retrieval.
     *
     * @param array $message The message containing image content
     * @return array The formatted image content
     */
    protected function formatImageContent(array $message): array
    {
        $content = [];
        $urls = $message['metadata']['image'] ?? [];

        foreach ($urls as $url) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $url,
                    'detail' => 'low',
                ],
            ];
        }

        return $content;
    }

    /**
     * Format audio content for storage or retrieval.
     *
     * @param array $message The message containing audio content
     * @return array The formatted audio content
     */
    protected function formatAudioContent(array $message): array
    {
        $content = [
            [
                'type' => 'text',
                'text' => $message['content'],
            ],
        ];

        $audio = $message['metadata']['audio'] ?? [];
        foreach ($audio as $a) {
            $content[] = [
                'type' => 'input_audio',
                'input_audio' => [
                    'data' => $a['buffer'],
                    'format' => $a['format'],
                ],
            ];
        }

        return $content;
    }

    /**
     * Format video content for storage or retrieval.
     *
     * @param array $message The message containing video content
     * @return array The formatted video content
     */
    protected function formatVideoContent(array $message): array
    {
        $content = [
            [
                'type' => 'text',
                'text' => $message['content'],
            ],
        ];

        $video = $message['metadata']['video'] ?? [];

        foreach ($video['frames'] ?? [] as $frame) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $frame,
                    'detail' => 'low',
                ],
            ];
        }

        if (isset($video['audio'])) {
            $content[] = [
                'type' => 'input_audio',
                'input_audio' => [
                    'data' => $video['audio']['buffer'],
                    'format' => $video['audio']['format'],
                ],
            ];
        }

        return $content;
    }

    // endregion
}

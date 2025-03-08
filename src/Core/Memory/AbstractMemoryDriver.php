<?php

namespace BuppleEngine\Core\Memory;

use BuppleEngine\Core\Memory\Contracts\MemoryDriverInterface;
use BuppleEngine\Core\Models\Memory;

abstract class AbstractMemoryDriver implements MemoryDriverInterface
{
    /**
     * The driver configuration.
     *
     * @var array
     */
    protected array $config;

    /**
     * The parent class for context.
     *
     * @var string|null
     */
    protected ?string $parentClass = null;

    /**
     * The parent ID for context.
     *
     * @var string|int|null
     */
    protected string|int|null $parentId = null;

    /**
     * Create a new memory driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Set the parent context for the memory.
     *
     * @param string $class
     * @param string|int $id
     * @return void
     */
    public function setParent(string $class, string|int $id): void
    {
        $this->parentClass = $class;
        $this->parentId = $id;
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

    /**
     * Format media content for storage or retrieval.
     *
     * @param array $message
     * @return array
     */
    protected function formatMediaContent(array $message): array
    {
        if ($message['type'] === 'text') {
            return $message;
        }

        // Handle different content types (image, audio, etc.)
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
     * Add a message to the chat history.
     *
     * @param string $role
     * @param string $content
     * @param string|null $type
     * @param array $metadata
     * @param string|null $messageId
     * @return void
     * @throws \RuntimeException
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
     * Get all messages from the chat history.
     *
     * @return array
     * @throws \RuntimeException
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
     * @return void
     * @throws \RuntimeException
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

    /**
     * Store a memory.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    abstract public function store(string $key, mixed $value): bool;

    /**
     * Retrieve a memory.
     *
     * @param string $key
     * @return mixed
     */
    abstract public function retrieve(string $key): mixed;

    /**
     * Check if a memory exists.
     *
     * @param string $key
     * @return bool
     */
    abstract public function exists(string $key): bool;

    /**
     * Delete a memory.
     *
     * @param string $key
     * @return bool
     */
    abstract public function delete(string $key): bool;

    /**
     * Format the role for the specific driver.
     *
     * @param string $role
     * @return string
     */
    abstract protected function formatRole(string $role): string;

    /**
     * Format a message for the specific driver.
     *
     * @param Memory $message
     * @return array
     */
    abstract protected function formatMessage(Memory $message): array;

    /**
     * Get the name of the driver.
     *
     * @return string
     */
    abstract protected function getDriverName(): string;

    public function addUserMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        $this->addMessage('user', $content, $type, $metadata, $messageId);
    }

    public function addAssistantMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        $this->addMessage('assistant', $content, $type, $metadata, $messageId);
    }

    public function addSystemMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void
    {
        $this->addMessage('system', $content, $type, $metadata, $messageId);
    }

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
}

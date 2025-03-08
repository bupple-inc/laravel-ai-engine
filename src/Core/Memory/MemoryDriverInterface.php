<?php

namespace BuppleEngine\Core\Memory;

interface MemoryDriverInterface
{
    /**
     * Store a memory.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function store(string $key, mixed $value): bool;

    /**
     * Retrieve a memory.
     *
     * @param string $key
     * @return mixed
     */
    public function retrieve(string $key): mixed;

    /**
     * Check if a memory exists.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Delete a memory.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * Add a message to the chat history.
     *
     * @param string $role
     * @param string $content
     * @param string|null $type
     * @param array $metadata
     * @param string|null $messageId
     * @return void
     */
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;

    /**
     * Get all messages from the chat history.
     *
     * @return array
     */
    public function getMessages(): array;

    /**
     * Clear all messages from the chat history.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Set the parent context for the memory.
     *
     * @param string $class
     * @param string|int $id
     * @return void
     */
    public function setParent(string $class, string|int $id): void;

    /**
     * Get the configuration for this driver.
     *
     * @return array
     */
    public function getConfig(): array;
}

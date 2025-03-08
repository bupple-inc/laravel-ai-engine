<?php

namespace BuppleEngine\Core\Drivers\Memory\Contracts;

interface MemoryDriverInterface
{
    public function addUserMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function addAssistantMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function addSystemMessage(string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null): void;
    public function getMessages(): array;
    public function clear(): void;
}

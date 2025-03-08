<?php

namespace BuppleEngine;

use BuppleEngine\Core\Drivers\Engine\ClaudeDriver;
use BuppleEngine\Core\Drivers\Engine\Contracts\EngineDriverInterface;
use BuppleEngine\Core\Drivers\Engine\GeminiDriver;
use BuppleEngine\Core\Drivers\Engine\OpenAIDriver;
use BuppleEngine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;
use BuppleEngine\Core\Drivers\Memory\MemoryManager;
use BuppleEngine\Core\Drivers\Stream\SseStreamDriver;
use BuppleEngine\Core\Helpers\JsonParserHelper;

class BuppleEngine
{
    /**
     * The memory manager instance.
     */
    protected MemoryManager $memory;

    /**
     * The configuration array.
     */
    protected array $config;

    /**
     * The SSE driver instance.
     */
    protected ?SseStreamDriver $sseDriver = null;

    /**
     * The JSON parser helper instance.
     */
    protected ?JsonParserHelper $jsonParserHelper = null;

    /**
     * The active chat driver instances.
     *
     * @var array<string, EngineDriverInterface>
     */
    protected array $chatDrivers = [];

    /**
     * Create a new engine instance.
     */
    public function __construct(MemoryManager $memory, array $config)
    {
        $this->memory = $memory;
        $this->config = $config;
    }

    /**
     * Get the memory manager instance.
     */
    public function memory(): MemoryManager
    {
        return $this->memory;
    }

    /**
     * Get a chat driver instance.
     *
     * @param string|null $name
     * @return EngineDriverInterface
     */
    public function ai(?string $name = null): EngineDriverInterface
    {
        $name = $name ?? $this->getDefaultChatDriver();

        if (!isset($this->chatDrivers[$name])) {
            $this->chatDrivers[$name] = $this->createChatDriver($name);
        }

        return $this->chatDrivers[$name];
    }

    /**
     * Alias for ai() method for backward compatibility.
     */
    public function chat(?string $name = null): EngineDriverInterface
    {
        return $this->ai($name);
    }

    /**
     * Get the SSE driver instance.
     */
    public function sse(): SseStreamDriver
    {
        if (!$this->sseDriver) {
            $this->sseDriver = new SseStreamDriver();
        }

        return $this->sseDriver;
    }

    /**
     * Get the JSON parser helper instance.
     */
    public function jsonParserHelper(): JsonParserHelper
    {
        if (!$this->jsonParserHelper) {
            $this->jsonParserHelper = new JsonParserHelper();
        }

        return $this->jsonParserHelper;
    }

    /**
     * Parse JSON string.
     */
    public function jsonParser(string $json): ?array
    {
        return $this->jsonParserHelper()->parse($json);
    }

    /**
     * Get a memory driver instance.
     *
     * @param string|null $driver
     * @return MemoryDriverInterface
     */
    public function driver(?string $driver = null): MemoryDriverInterface
    {
        return $this->memory->driver($driver);
    }

    /**
     * Get the default chat driver name.
     */
    protected function getDefaultChatDriver(): string
    {
        return $this->config['default']['chat'] ?? 'openai';
    }

    /**
     * Create a new chat driver instance.
     */
    protected function createChatDriver(string $driver): EngineDriverInterface
    {
        $config = $this->config[$driver] ?? [];

        return match ($driver) {
            'openai' => new OpenAIDriver($config),
            'gemini' => new GeminiDriver($config),
            'claude' => new ClaudeDriver($config),
            default => throw new \InvalidArgumentException("Driver [{$driver}] not supported."),
        };
    }

    /**
     * Get the configuration.
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }
}

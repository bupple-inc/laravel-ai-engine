<?php

namespace Bupple\Engine;

use Bupple\Engine\Core\Drivers\ClaudeDriver;
use Bupple\Engine\Core\Drivers\Contracts\ChatDriverInterface;
use Bupple\Engine\Core\Drivers\GeminiDriver;
use Bupple\Engine\Core\Drivers\OpenAIDriver;
use Bupple\Engine\Core\Memory\Contracts\MemoryDriverInterface;
use Bupple\Engine\Core\Drivers\SseDriver;
use Bupple\Engine\Core\Helpers\JsonParserHelper;
use Bupple\Engine\Core\Memory\MemoryManager;

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
    protected ?SseDriver $sseDriver = null;

    /**
     * The JSON parser helper instance.
     */
    protected ?JsonParserHelper $jsonParserHelper = null;

    /**
     * The active chat driver instances.
     *
     * @var array<string, ChatDriverInterface>
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
     * Get the SSE driver instance.
     */
    public function sse(): SseDriver
    {
        if (!$this->sseDriver) {
            $this->sseDriver = new SseDriver();
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
     *
     * @param string $json
     * @return array|null
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
     * Get a chat driver instance.
     *
     * @param string|null $name
     * @return ChatDriverInterface
     * @throws \InvalidArgumentException|\RuntimeException
     */
    public function chat(?string $name = null): ChatDriverInterface
    {
        $name = $name ?? $this->getDefaultChatDriver();

        if (!isset($this->chatDrivers[$name])) {
            $this->chatDrivers[$name] = $this->createChatDriver($name);
        }

        return $this->chatDrivers[$name];
    }

    /**
     * Get the default chat driver name.
     *
     * @return string
     */
    protected function getDefaultChatDriver(): string
    {
        return $this->config['default']['chat'] ?? 'openai';
    }

    /**
     * Create a new chat driver instance.
     *
     * @param string $driver
     * @return ChatDriverInterface
     * @throws \InvalidArgumentException|\RuntimeException
     */
    protected function createChatDriver(string $driver): ChatDriverInterface
    {
        $config = $this->config[$driver] ?? [];

        return match ($driver) {
            'openai' => new OpenAIDriver($config),
            'gemini' => new GeminiDriver($config),
            'claude' => new ClaudeDriver($config),
            default => throw new \InvalidArgumentException("Chat driver [{$driver}] not supported."),
        };
    }

    /**
     * Get the configuration.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }
}

<?php

namespace Bupple\Engine;

use Bupple\Engine\Core\Drivers\Engine\Claude\ClaudeDriver;
use Bupple\Engine\Core\Drivers\Engine\Contracts\EngineDriverInterface;
use Bupple\Engine\Core\Drivers\Engine\Gemini\GeminiDriver;
use Bupple\Engine\Core\Drivers\Engine\OpenAI\OpenAIDriver;
use Bupple\Engine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;
use Bupple\Engine\Core\Drivers\Memory\MemoryManager;
use Bupple\Engine\Core\Drivers\Stream\SseStreamDriver;
use Bupple\Engine\Core\Helpers\JsonParserHelper;

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
     * The active engine driver instances.
     *
     * @var array<string, EngineDriverInterface>
     */
    protected array $engineDrivers = [];

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
     * Get an engine driver instance.
     *
     * @param string|null $name
     * @return EngineDriverInterface
     */
    public function engine(?string $name = null): EngineDriverInterface
    {
        $name = $name ?? $this->getDefaultEngineDriver();

        if (!isset($this->engineDrivers[$name])) {
            $this->engineDrivers[$name] = $this->createEngineDriver($name);
        }

        return $this->engineDrivers[$name];
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
     * Get the default engine driver name.
     */
    protected function getDefaultEngineDriver(): string
    {
        return $this->config['engine']['default'] ?? 'openai';
    }

    /**
     * Create a new engine driver instance.
     */
    protected function createEngineDriver(string $driver): EngineDriverInterface
    {
        $config = $this->config['engine'][$driver] ?? [];

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

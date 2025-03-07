<?php

namespace Bupple\Engine\Core\Memory;

use Bupple\Engine\Core\Memory\Contracts\MemoryDriverInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * @method bool store(string $key, mixed $value)
 * @method mixed retrieve(string $key)
 * @method bool exists(string $key)
 * @method bool delete(string $key)
 * @method void addMessage(string $role, string $content, ?string $type = 'text', array $metadata = [], ?string $messageId = null)
 * @method array getMessages()
 * @method void clear()
 * @method void setParent(string $class, string|int $id)
 */
class MemoryManager
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected array $config;

    /**
     * The active driver instances.
     *
     * @var array<string, MemoryDriverInterface>
     */
    protected array $drivers = [];

    /**
     * Create a new memory manager instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a driver instance.
     *
     * @param string|null $name
     * @return MemoryDriverInterface
     */
    public function driver(?string $name = null): MemoryDriverInterface
    {
        $name = $name ?? $this->getDefaultDriver();

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config['default']['memory'] ?? 'openai';
    }

    /**
     * Set the default driver name.
     *
     * @param string $name
     * @return void
     */
    public function setDefaultDriver(string $name): void
    {
        $this->config['default']['memory'] = $name;
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     * @return MemoryDriverInterface
     * @throws InvalidArgumentException|RuntimeException
     */
    protected function createDriver(string $driver): MemoryDriverInterface
    {
        $config = $this->config[$driver] ?? [];

        $driverClass = match ($driver) {
            'openai' => __NAMESPACE__ . '\\OpenAIMemoryDriver',
            'gemini' => __NAMESPACE__ . '\\GeminiMemoryDriver',
            'claude' => __NAMESPACE__ . '\\ClaudeMemoryDriver',
            default => throw new InvalidArgumentException("Memory driver [{$driver}] not supported."),
        };

        if (!class_exists($driverClass)) {
            throw new RuntimeException("Driver class [{$driverClass}] does not exist.");
        }

        if (!is_subclass_of($driverClass, MemoryDriverInterface::class)) {
            throw new RuntimeException("Driver class [{$driverClass}] must implement MemoryDriverInterface.");
        }

        /** @var MemoryDriverInterface */
        return new $driverClass($config);
    }

    /**
     * Get the configuration for the specified driver.
     *
     * @param string|null $name
     * @return array
     */
    public function getConfig(?string $name = null): array
    {
        $name = $name ?? $this->getDefaultDriver();
        return $this->config[$name] ?? [];
    }

    /**
     * Dynamically call methods on the default driver.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}

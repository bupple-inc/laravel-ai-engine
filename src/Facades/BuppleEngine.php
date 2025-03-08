<?php

namespace BuppleEngine\Facades;

use BuppleEngine\Core\Memory\MemoryManager;
use BuppleEngine\Core\Memory\Contracts\MemoryDriverInterface;
use BuppleEngine\Core\Drivers\Contracts\ChatDriverInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MemoryManager memory()
 * @method static MemoryDriverInterface driver(string|null $driver = null)
 * @method static ChatDriverInterface chat(string|null $driver = null)
 * @method static \BuppleEngine\Core\Drivers\Stream\SseStreamDriver sse()
 * @method static \BuppleEngine\Core\Helpers\JsonParserHelper jsonParserHelper()
 * @method static array|null jsonParser(string $json)
 * @method static mixed config(string|null $key = null, mixed $default = null)
 * 
 * @see \BuppleEngine\BuppleEngine
 */
class BuppleEngine extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bupple.engine';
    }
}

<?php

namespace BuppleEngine\Facades;

use BuppleEngine\Core\Drivers\Engine\Contracts\EngineDriverInterface;
use BuppleEngine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;
use BuppleEngine\Core\Drivers\Memory\MemoryManager;
use BuppleEngine\Core\Drivers\Stream\SseStreamDriver;
use BuppleEngine\Core\Helpers\JsonParserHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MemoryManager memory()
 * @method static MemoryDriverInterface driver(string|null $driver = null)
 * @method static EngineDriverInterface engine(string|null $name = null)
 * @method static SseStreamDriver sse()
 * @method static JsonParserHelper jsonParserHelper()
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

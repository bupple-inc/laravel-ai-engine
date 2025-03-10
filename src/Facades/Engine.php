<?php

namespace Bupple\Engine\Facades;

use Bupple\Engine\Core\Drivers\Engine\Contracts\EngineDriverInterface;
use Bupple\Engine\Core\Drivers\Memory\Contracts\MemoryDriverInterface;
use Bupple\Engine\Core\Drivers\Memory\MemoryManager;
use Bupple\Engine\Core\Drivers\Stream\SseStreamDriver;
use Bupple\Engine\Core\Helpers\JsonParserHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MemoryManager memory()
 * @method static MemoryDriverInterface driver(string|null $driver = null)
 * @method static EngineDriverInterface engine(string|null $name = null)
 * @method static SseStreamDriver sse()
 * @method static JsonParserHelper jsonParserHelper()
 * @method static mixed config(string|null $key = null, mixed $default = null)
 * 
 * @see \Bupple\Engine\BuppleEngine
 */
class Engine extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bupple.engine';
    }
}

<?php

namespace Bupple\Engine\Facades;

use Bupple\Engine\Core\Memory\MemoryManager;
use Bupple\Engine\Core\Memory\Contracts\MemoryDriverInterface;
use Bupple\Engine\Core\Drivers\Contracts\ChatDriverInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MemoryManager memory()
 * @method static MemoryDriverInterface driver(string|null $driver = null)
 * @method static ChatDriverInterface chat(string|null $driver = null)
 * @method static \Bupple\Engine\Core\Drivers\SseDriver sse()
 * @method static \Bupple\Engine\Core\Helpers\JsonParserHelper jsonParserHelper()
 * @method static array|null jsonParser(string $json)
 * @method static mixed config(string|null $key = null, mixed $default = null)
 * 
 * @see \Bupple\Engine\BuppleEngine
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

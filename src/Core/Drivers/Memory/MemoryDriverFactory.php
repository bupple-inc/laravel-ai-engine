<?php

namespace Bupple\Engine\Core\Drivers\Memory;

use InvalidArgumentException;
use Bupple\Engine\Core\Drivers\Engine\OpenAI\OpenAIMemoryDriver;
use Bupple\Engine\Core\Drivers\Engine\Gemini\GeminiMemoryDriver;
use Bupple\Engine\Core\Drivers\Engine\Claude\ClaudeMemoryDriver;

class MemoryDriverFactory
{
    public static function create(string $driver, mixed $parentModel): AbstractMemoryDriver
    {
        return match ($driver) {
            'openai' => new OpenAIMemoryDriver($parentModel),
            'gemini' => new GeminiMemoryDriver($parentModel),
            'claude' => new ClaudeMemoryDriver($parentModel),
            default => throw new InvalidArgumentException("Unsupported memory driver: {$driver}"),
        };
    }
}

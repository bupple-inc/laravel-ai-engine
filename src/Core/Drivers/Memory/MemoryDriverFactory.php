<?php

namespace BuppleEngine\Core\Drivers\Memory;

use InvalidArgumentException;
use BuppleEngine\Core\Drivers\Engine\Memory\OpenAIMemoryDriver;
use BuppleEngine\Core\Drivers\Engine\Memory\GeminiMemoryDriver;
use BuppleEngine\Core\Drivers\Engine\Memory\ClaudeMemoryDriver;

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

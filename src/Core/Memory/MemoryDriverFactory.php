<?php

namespace BuppleEngine\Core\Memory;

use InvalidArgumentException;

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

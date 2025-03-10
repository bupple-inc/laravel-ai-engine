<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the engine drivers.
    | Supported engines: "openai", "gemini", "claude"
    |
    */

    'engine' => [
        'default' => env('BUPPLE_DEFAULT_ENGINE_DRIVER', 'openai'),

        /*
        |--------------------------------------------------------------------------
        | OpenAI Configuration
        |--------------------------------------------------------------------------
        |
        | Here you can configure the OpenAI engine driver.
        |
        */

        'openai' => [
            'api_key' => env('BUPPLE_ENGINE_OPENAI_API_KEY'),
            'model' => env('BUPPLE_ENGINE_OPENAI_MODEL', 'gpt-4'),
            'temperature' => env('BUPPLE_ENGINE_OPENAI_TEMPERATURE', 0.7),
            'max_tokens' => env('BUPPLE_ENGINE_OPENAI_MAX_TOKENS', 1000),
            'organization_id' => env('BUPPLE_ENGINE_OPENAI_ORGANIZATION_ID', null),
        ],

        /*
        |--------------------------------------------------------------------------
        | Google Gemini Configuration
        |--------------------------------------------------------------------------
        |
        | Here you can configure the Google Gemini engine driver.
        |
        */

        'gemini' => [
            'api_key' => env('BUPPLE_ENGINE_GEMINI_API_KEY'),
            'model' => env('BUPPLE_ENGINE_GEMINI_MODEL', 'gemini-pro'),
            'temperature' => env('BUPPLE_ENGINE_GEMINI_TEMPERATURE', 0.7),
            'max_tokens' => env('BUPPLE_ENGINE_GEMINI_MAX_TOKENS', 1000),
            'project_id' => env('BUPPLE_ENGINE_GEMINI_PROJECT_ID', null),
        ],

        /*
        |--------------------------------------------------------------------------
        | Anthropic Claude Configuration
        |--------------------------------------------------------------------------
        |
        | Here you can configure the Anthropic Claude engine driver.
        |
        */

        'claude' => [
            'api_key' => env('BUPPLE_ENGINE_CLAUDE_API_KEY'),
            'model' => env('BUPPLE_ENGINE_CLAUDE_MODEL', 'claude-3-opus-20240229'),
            'temperature' => env('BUPPLE_ENGINE_CLAUDE_TEMPERATURE', 0.7),
            'max_tokens' => env('BUPPLE_ENGINE_CLAUDE_MAX_TOKENS', 1000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the memory driver for the package.
    | Supported drivers: "file", "database", "redis"
    |
    */

    'memory' => [
        'default' => env('BUPPLE_MEMORY_DRIVER', 'file'),

        /*
        |--------------------------------------------------------------------------
        | Database Configuration
        |--------------------------------------------------------------------------
        |
        | Here you can specify the database configuration for the package.
        | Set mongodb_enabled to true if you want to use MongoDB as your database.
        | When using MongoDB, migrations will be skipped automatically.
        |
        */

        'database' => [
            'mongodb_enabled' => env('BUPPLE_MEMORY_DB_MONGODB_ENABLED', false),
            'connection' => env('BUPPLE_MEMORY_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
            'table_name' => env('BUPPLE_MEMORY_DB_TABLE_NAME', 'engine_memory'),
        ],

        /*
        |--------------------------------------------------------------------------
        | File Configuration
        |--------------------------------------------------------------------------
        |
        | Here you can configure the file driver for the package.
        |
        */

        'file' => [
            'path' => storage_path(env('BUPPLE_MEMORY_FILE_PATH', 'app/engine-memory')),
        ],

        /*
        |--------------------------------------------------------------------------
        | Redis Configuration
        |--------------------------------------------------------------------------
        |
        | Here you can configure the Redis driver for the package.
        |
        */

        'redis' => [
            'connection' => env('BUPPLE_MEMORY_REDIS_CONNECTION', 'redis'),
            'key' => env('BUPPLE_MEMORY_REDIS_KEY', 'engine_memory'),
            'ttl' => env('BUPPLE_MEMORY_REDIS_TTL', 60 * 60 * 24 * 30), // 30 days
        ],
    ],
];

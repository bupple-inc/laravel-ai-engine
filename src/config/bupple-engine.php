<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Drivers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the default engine driver.
    | Supported engines: "openai", "gemini", "claude"
    |
    */

    'default' => [
        'engine' => env('BUPPLE_ENGINE_DRIVER', 'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'organization_id' => env('OPENAI_ORGANIZATION_ID', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini Configuration
    |--------------------------------------------------------------------------
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
        'project_id' => env('GEMINI_PROJECT_ID', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Anthropic Claude Configuration
    |--------------------------------------------------------------------------
    */

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-3-opus-20240229'),
        'temperature' => env('CLAUDE_TEMPERATURE', 0.7),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify the database configuration for the package.
    | Set use_mongodb to true if you want to use MongoDB as your database.
    | When using MongoDB, migrations will be skipped automatically.
    |
    */
    'database' => [
        'mongodb_enabled' => env('BUPPLE_ENGINE_DB_MONGODB_ENABLED', false),
        'connection' => env('BUPPLE_ENGINE_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
        'memory_table' => env('BUPPLE_ENGINE_DB_MEMORY_TABLE', 'engine_memory'),
        'prompt_table' => env('BUPPLE_ENGINE_DB_PROMPT_TABLE', 'engine_prompt'),
        'prompt_version_table' => env('BUPPLE_ENGINE_DB_PROMPT_VERSION_TABLE', 'engine_prompt_version'),
    ],
];

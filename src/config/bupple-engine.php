<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Drivers Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the default drivers for both chat and memory.
    | Supported chat drivers: "openai", "gemini", "claude"
    | Supported memory drivers: "openai", "gemini", "claude"
    |
    */

    'default' => [
        'chat' => env('BUPPLE_CHAT_DRIVER', 'openai'),
        'memory' => env('BUPPLE_MEMORY_DRIVER', 'openai'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Memory Configuration
    |--------------------------------------------------------------------------
    |
    | Configure memory settings for each supported engine.
    | You can specify different models and settings for memory handling.
    |
    */

    'memory' => [
        'openai' => [
            'model' => env('BUPPLE_MEMORY_OPENAI_MODEL', 'gpt-4'),
            'temperature' => env('BUPPLE_MEMORY_OPENAI_TEMPERATURE', 0.7),
            'max_tokens' => env('BUPPLE_MEMORY_OPENAI_MAX_TOKENS', 1000),
        ],
        'gemini' => [
            'model' => env('BUPPLE_MEMORY_GEMINI_MODEL', 'gemini-pro'),
            'temperature' => env('BUPPLE_MEMORY_GEMINI_TEMPERATURE', 0.7),
            'max_tokens' => env('BUPPLE_MEMORY_GEMINI_MAX_TOKENS', 1000),
        ],
        'claude' => [
            'model' => env('BUPPLE_MEMORY_CLAUDE_MODEL', 'claude-3-opus-20240229'),
            'temperature' => env('BUPPLE_MEMORY_CLAUDE_TEMPERATURE', 0.7),
            'max_tokens' => env('BUPPLE_MEMORY_CLAUDE_MAX_TOKENS', 1000),
        ],
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
        'use_mongodb' => env('BUPPLE_USE_MONGODB', false),
        'connection' => env('BUPPLE_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),
        'memory_table' => env('BUPPLE_MEMORY_TABLE', 'bupple_memories'),
        'chat_table' => env('BUPPLE_CHAT_TABLE', 'bupple_chats'),
    ],
];

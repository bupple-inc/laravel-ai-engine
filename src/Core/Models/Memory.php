<?php

namespace Bupple\Engine\Core\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use MongoDB\Laravel\Eloquent\Model as MongoDBModel;
use RuntimeException;

if (config('bupple-engine.memory.database.mongodb_enabled')) {
    if (!class_exists(MongoDBModel::class)) {
        throw new RuntimeException(
            'MongoDB support is enabled but the package "mongodb/laravel-mongodb" is not installed. ' .
                'Please run: composer require mongodb/laravel-mongodb'
        );
    }
    class Memory extends MongoDBModel
    {
        public function __construct(array $attributes = [])
        {
            parent::__construct($attributes);
            $this->setConnection(config('bupple-engine.memory.database.connection'));
            $this->setTable(config('bupple-engine.memory.database.table_name'));
        }

        protected $fillable = [
            'parent_class',
            'parent_id',
            'message_id',
            'role',
            'content',
            'type',
            'metadata',
            'driver',
        ];

        protected $casts = [
            'metadata' => 'object',
        ];

        public function setMetadataAttribute($value)
        {
            $this->attributes['metadata'] = is_array($value) ? (object) $value : $value;
        }

        public function getMetadataAttribute($value)
        {
            return is_object($value) ? (array) $value : (is_string($value) ? json_decode($value, true) : $value);
        }
    }
} else {
    class Memory extends EloquentModel
    {
        public function __construct(array $attributes = [])
        {
            parent::__construct($attributes);
            $this->setConnection(config('bupple-engine.memory.database.connection'));
            $this->setTable(config('bupple-engine.memory.database.table_name'));
        }

        protected $fillable = [
            'parent_class',
            'parent_id',
            'message_id',
            'role',
            'content',
            'type',
            'metadata',
            'driver',
        ];

        protected $casts = [
            'metadata' => 'array',
        ];
    }
}

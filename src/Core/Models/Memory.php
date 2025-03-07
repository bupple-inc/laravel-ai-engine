<?php

namespace Bupple\Engine\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    protected $table = 'engine_memory';

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

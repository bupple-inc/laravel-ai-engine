<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('bupple-engine.memory.database.mongodb_enabled')) {
            return;
        }

        Schema::create(config('bupple-engine.memory.database.table_name'), function (Blueprint $table) {
            $table->id();
            $table->string('parent_class');
            $table->string('parent_id');
            $table->string('message_id')->nullable();
            $table->string('role');
            $table->text('content');
            $table->string('type')->default('text');
            $table->json('metadata')->nullable();
            $table->string('driver');
            $table->timestamps();

            $table->index(['parent_class', 'parent_id', 'driver']);
        });
    }

    public function down(): void
    {
        if (config('bupple-engine.memory.database.mongodb_enabled')) {
            return;
        }

        Schema::dropIfExists(config('bupple-engine.memory.database.table_name'));
    }
};

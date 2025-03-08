<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engine_memory', function (Blueprint $table) {
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
        Schema::dropIfExists('engine_memory');
    }
};

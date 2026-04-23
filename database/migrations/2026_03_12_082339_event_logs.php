<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 100);
            $table->string('user_id', 100)->nullable();
            $table->string('user_role', 50)->nullable();
            $table->text('description');
            $table->json('context')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('event_type',             'idx_event_logs_type');
            $table->index('user_id',                'idx_event_logs_user');
            $table->index('created_at',             'idx_event_logs_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->string('election_id', 100);
                $table->string('generated_by', 100)->nullable();
                $table->string('report_type', 100);
                $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_format', 10)->nullable();
                $table->unsignedBigInteger('file_size_bytes')->nullable();
                $table->json('filters')->nullable();
                $table->json('summary')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
                $table->index('election_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

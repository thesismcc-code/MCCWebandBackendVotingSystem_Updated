<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fingerprints', function (Blueprint $table) {
            $table->id();
            $table->integer('fid')->unique();
            $table->string('name')->default('');
            $table->text('template'); // base64 ZKFinger template
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprints');
    }
};

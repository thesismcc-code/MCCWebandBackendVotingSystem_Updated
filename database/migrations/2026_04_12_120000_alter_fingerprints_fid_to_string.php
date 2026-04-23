<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fingerprints', function (Blueprint $table) {
            $table->string('fid', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('fingerprints', function (Blueprint $table) {
            $table->integer('fid')->change();
        });
    }
};

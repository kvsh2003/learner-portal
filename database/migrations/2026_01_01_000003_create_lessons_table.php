<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type', 20)->default('video'); // video | reading | quiz
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->unsignedSmallInteger('position');
            $table->string('content_url')->nullable();
            $table->timestamps();

            $table->unique(['module_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('klasses', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->year('year')->default(date('Y'));
            $table->enum('department',['Science', 'Arts', 'Languages', 'commercials'])->default('Science');
            $table->json('teachers')->nullable()->change();
            $table->foreignId('teacher_id')->nullable();
            $table->json('subjects')->nullable()->change();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klasses');
    }
};

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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('category', ['activity', 'event'])->nullable(); 
            $table->enum('type', ['academic', 'sports', 'cultural', 'meeting', 'ceremony', 'club', 'other'])->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable();
            $table->json('target_classes')->nullable(); // ['all', 'grade_7', 'grade_8']
            $table->boolean('requires_registration')->default(false);
            $table->dateTime('registration_deadline')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->text('requirements')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

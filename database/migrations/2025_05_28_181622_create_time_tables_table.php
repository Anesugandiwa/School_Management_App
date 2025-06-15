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
        Schema::create('time_tables', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('klass_id');
            $table->foreignId('subject_id');
            $table->foreignId('teacher_id');
            $table->string('day_of_week'); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_break')->default(false);
            $table->string('academic_year')->default(now()->year);
            $table->enum('term', ['1st Term', '2nd Term', '3rd Term'])->default('1st Term')->nullable();
            $table->integer('period')->nullable(); 
            $table->string('period_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_tables');
    }
};

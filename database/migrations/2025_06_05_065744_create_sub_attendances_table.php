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
        Schema::create('sub_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('klass_id');
            $table->foreignId('subject_id');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late']);
            // preventing the duplicate attendance records for the students
            $table->unique(['student_id', 'klass_id', 'subject_id', 'date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_attendances');
    }
};

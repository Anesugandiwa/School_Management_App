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
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('subject_id');
            $table->foreignId('klass_id');
            $table->string('exam_type')->nullable();
            $table->string('term')->nullable();
            $table->string('year')->nullable();
            $table->decimal('marks', 5, 2)->nullable();
            $table->decimal('total_marks', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};

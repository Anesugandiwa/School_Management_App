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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender',['Male', 'Female', 'Other'])->default('Other');
            $table->date('Date_Of_Birth')->nullable();
            $table->string('national_id')->nullable();
            $table->email('email')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->enum('department',['Science', 'Arts', 'Languages', 'commercials'])->default('Science');
            $table->string(' password');
            $table->foreignId('user_id');
            $table->string('subject')->nullable();
            $table->foreignId('klass_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('student_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('department', 100);
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->string('contact_number', 30)->nullable();
            $table->string('status', 20)->default('Active'); // Active, Inactive, Graduated
            $table->timestamps();

            // Indexes for fast searching and joins
            $table->index('student_number');
            $table->index('status');
            $table->index(['last_name', 'first_name']);
        });

        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('employee_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('contact_number', 30)->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            // Indexes
            $table->index('employee_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
        Schema::dropIfExists('athletes');
    }
};

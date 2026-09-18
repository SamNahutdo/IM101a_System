<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athlete_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('student_id', 30)->unique();
            $table->string('emergency_contact_name', 100);
            $table->string('emergency_contact_phone', 20);
            $table->enum('medical_clearance_status', ['Cleared', 'Pending', 'Restricted'])->default('Pending');
            $table->unsignedTinyInteger('year_level');
            $table->timestamps();
        });

        Schema::create('coach_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('employee_id', 30)->unique();
            $table->string('specialization', 100);
            $table->string('department', 100);
            $table->string('license_number', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('sport', 60);
            $table->foreignId('coach_id')->constrained('coach_profiles')->onDelete('restrict');
            $table->enum('gender_category', ['Men', 'Women', 'Co-ed']);
            $table->enum('status', ['Active', 'Inactive', 'Offseason'])->default('Active');
            $table->timestamps();
        });

        // N:M Junction Table with Rich Intersection Attributes
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('athlete_id')->constrained('athlete_profiles')->onDelete('cascade');
            $table->string('jersey_number', 10)->nullable();
            $table->string('position', 50)->nullable();
            $table->enum('membership_status', ['Active', 'Injured', 'Suspended', 'Former'])->default('Active');
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->unique(['team_id', 'athlete_id', 'membership_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('coach_profiles');
        Schema::dropIfExists('athlete_profiles');
    }
};

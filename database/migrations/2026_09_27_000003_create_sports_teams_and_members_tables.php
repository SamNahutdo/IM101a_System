<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('sport_name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->onDelete('restrict');
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->onDelete('set null');
            $table->string('team_name', 100);
            $table->string('school_year', 20)->default('2026-2027');
            $table->string('status', 20)->default('Active');
            $table->timestamps();

            $table->index('sport_id');
            $table->index('coach_id');
            $table->index('status');
        });

        // Many-to-Many Junction Table 1: Teams <-> Athletes with intersection attributes
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('cascade');
            $table->date('joined_at');
            $table->string('position', 50)->nullable();
            $table->string('status', 20)->default('Active'); // Active, Inactive, Former
            $table->timestamps();

            $table->unique(['team_id', 'athlete_id']);
            $table->index('team_id');
            $table->index('athlete_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('sports');
    }
};

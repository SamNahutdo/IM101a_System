<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->onDelete('restrict');
            $table->string('equipment_code', 50)->unique();
            $table->string('equipment_name', 150);
            $table->string('category', 100);
            $table->unsignedInteger('quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->string('condition', 50)->default('Good'); // New, Good, Fair, Damaged
            $table->string('status', 30)->default('Available'); // Available, In Use, Under Maintenance, Retired
            $table->timestamps();

            // Frequently searched/joined indexes
            $table->index('sport_id');
            $table->index('equipment_code');
            $table->index('status');
            $table->index('category');
            $table->index(['sport_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

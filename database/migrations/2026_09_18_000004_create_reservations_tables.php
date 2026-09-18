<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 30)->unique();
            $table->foreignId('requester_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('purpose');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed'])->default('Pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            // Strategic index for reservation overlap detection
            $table->index(['start_time', 'end_time', 'status'], 'idx_res_dates_status');
        });

        // N:M Junction Table: Reservations ↔ Equipment
        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->unsignedInteger('requested_quantity')->default(1);
            $table->unsignedInteger('approved_quantity')->default(0);
            $table->enum('item_status', ['Pending', 'Approved', 'Unavailable', 'Cancelled'])->default('Pending');
            $table->string('notes', 255)->nullable();
            $table->unique(['reservation_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
        Schema::dropIfExists('reservations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 30)->unique();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('set null');
            $table->foreignId('borrower_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('processed_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('checkout_time');
            $table->dateTime('expected_return_time');
            $table->dateTime('actual_return_time')->nullable();
            $table->enum('status', ['Active', 'Completed', 'Overdue', 'Defaulted'])->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Strategic index for fast overdue identification
            $table->index(['status', 'expected_return_time'], 'idx_borrowing_status_expected');
        });

        // N:M Junction Table: BorrowingTransactions ↔ Equipment with condition transitions
        Schema::create('borrowing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('borrowing_transactions')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->enum('checkout_condition', ['New', 'Excellent', 'Good', 'Fair', 'Poor']);
            $table->enum('return_condition', ['New', 'Excellent', 'Good', 'Fair', 'Poor'])->nullable();
            $table->foreignId('return_inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('returned_at')->nullable();
            $table->enum('status', ['Borrowed', 'Returned', 'Damaged', 'Lost'])->default('Borrowed');
            $table->string('remarks', 255)->nullable();
            $table->unique(['transaction_id', 'equipment_id']);
        });

        Schema::create('equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->enum('assignable_type', ['Athlete', 'Team']);
            $table->foreignId('athlete_id')->nullable()->constrained('athlete_profiles')->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('restrict');
            $table->date('assigned_date');
            $table->date('expected_end_date');
            $table->date('actual_return_date')->nullable();
            $table->enum('assignment_status', ['Active', 'Returned', 'Revoked'])->default('Active');
            $table->enum('condition_on_assignment', ['New', 'Excellent', 'Good', 'Fair', 'Poor'])->default('Good');
            $table->enum('condition_on_return', ['New', 'Excellent', 'Good', 'Fair', 'Poor'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_assignments');
        Schema::dropIfExists('borrowing_items');
        Schema::dropIfExists('borrowing_transactions');
    }
};

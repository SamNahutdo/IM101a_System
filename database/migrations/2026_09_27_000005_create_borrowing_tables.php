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
            $table->foreignId('athlete_id')->constrained('athletes')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->string('status', 30)->default('Pending'); // Pending, Approved, Borrowed, Returned, Overdue, Cancelled
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Strategic indexes for reports and queries
            $table->index('athlete_id');
            $table->index('approved_by');
            $table->index('status');
            $table->index('expected_return_date');
            $table->index(['status', 'expected_return_date']);
        });

        // Many-to-Many Junction Table 2: Borrowing Transactions <-> Equipment with intersection attributes
        Schema::create('borrowing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_transaction_id')->constrained('borrowing_transactions')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('condition_before', 50)->default('Good');
            $table->string('condition_after', 50)->nullable();
            $table->unsignedInteger('returned_quantity')->default(0);
            $table->timestamps();

            $table->index('borrowing_transaction_id');
            $table->index('equipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_items');
        Schema::dropIfExists('borrowing_transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('maintenance_type', 80); // Inspection, Repair, Cleaning, Restring, Repaint
            $table->text('description');
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->string('status', 30)->default('Scheduled'); // Scheduled, In Progress, Completed, Cancelled
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('equipment_id');
            $table->index('status');
            $table->index('scheduled_date');
        });

        // Audit Logs for database triggers and system events
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 50); // INSERT, UPDATE, DELETE
            $table->string('table_name', 50);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('table_name');
            $table->index(['table_name', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('maintenance_records');
    }
};

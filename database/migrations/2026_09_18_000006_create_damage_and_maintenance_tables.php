<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code', 30)->unique();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->foreignId('borrowing_item_id')->nullable()->constrained('borrowing_items')->onDelete('set null');
            $table->foreignId('reported_by')->constrained('users')->onDelete('restrict');
            $table->string('damage_type', 80);
            $table->enum('severity', ['Minor', 'Moderate', 'Severe']);
            $table->text('description');
            $table->dateTime('reported_at')->useCurrent();
            $table->enum('status', ['Reported', 'Under Review', 'Repair Required', 'Resolved', 'Closed'])->default('Reported');
            $table->text('resolution')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->foreignId('damage_report_id')->nullable()->constrained('damage_reports')->onDelete('set null');
            $table->string('maintenance_type', 80);
            $table->date('scheduled_date');
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->foreignId('assigned_staff_id')->constrained('users')->onDelete('restrict');
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->text('description');
            $table->text('result_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('damage_reports');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('code', 20)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('equipment_locations', function (Blueprint $table) {
            $table->id();
            $table->string('building', 100);
            $table->string('room', 50);
            $table->string('shelf_bin', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->unique(['building', 'room', 'shelf_bin']);
        });

        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->string('name', 120);
            $table->foreignId('category_id')->constrained('equipment_categories')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('equipment_locations')->onDelete('restrict');
            $table->string('serial_number', 100)->nullable()->unique();
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 10, 2)->default(0.00);
            $table->enum('current_condition', ['New', 'Excellent', 'Good', 'Fair', 'Poor'])->default('Good');
            $table->enum('status', [
                'Available',
                'Reserved',
                'Borrowed',
                'Assigned',
                'Maintenance',
                'Damaged',
                'Lost',
                'Retired'
            ])->default('Available');
            $table->text('description')->nullable();
            $table->timestamps();

            // Strategic index for rapid status + category lookup
            $table->index(['status', 'category_id'], 'idx_equipment_status_cat');
        });

        Schema::create('equipment_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('old_status', 50);
            $table->string('new_status', 50);
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_status_history');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('equipment_locations');
        Schema::dropIfExists('equipment_categories');
    }
};

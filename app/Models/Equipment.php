<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'location_id',
        'serial_number',
        'brand',
        'model',
        'purchase_date',
        'purchase_cost',
        'current_condition',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(EquipmentLocation::class, 'location_id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(EquipmentStatusHistory::class, 'equipment_id')->latest();
    }

    public function reservationItems(): HasMany
    {
        return $this->hasMany(ReservationItem::class, 'equipment_id');
    }

    public function borrowingItems(): HasMany
    {
        return $this->hasMany(BorrowingItem::class, 'equipment_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class, 'equipment_id');
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'equipment_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'equipment_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }
}

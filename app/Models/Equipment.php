<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'sport_id',
        'equipment_code',
        'equipment_name',
        'category',
        'quantity',
        'available_quantity',
        'condition',
        'status',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    // Many-to-Many Relationship 2: Equipment <-> BorrowingTransactions via borrowing_items
    public function borrowingTransactions()
    {
        return $this->belongsToMany(BorrowingTransaction::class, 'borrowing_items')
            ->withPivot(['id', 'quantity', 'condition_before', 'condition_after', 'returned_quantity'])
            ->withTimestamps();
    }

    public function borrowingItems()
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('available_quantity', '>', 0)->where('status', 'Available');
    }

    public function isAvailable(): bool
    {
        return $this->available_quantity > 0 && $this->status === 'Available';
    }
}

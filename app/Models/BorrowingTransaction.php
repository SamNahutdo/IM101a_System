<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'approved_by',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
        ];
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Many-to-Many Relationship 2: Borrowing Transactions <-> Equipment via borrowing_items
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'borrowing_items')
            ->withPivot(['id', 'quantity', 'condition_before', 'condition_after', 'returned_quantity'])
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function isOverdue(): bool
    {
        if ($this->status === 'Returned' || $this->actual_return_date !== null) {
            return false;
        }
        return $this->expected_return_date < now()->toDateString();
    }
}

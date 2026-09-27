<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_transaction_id',
        'equipment_id',
        'quantity',
        'condition_before',
        'condition_after',
        'returned_quantity',
    ];

    public function borrowingTransaction()
    {
        return $this->belongsTo(BorrowingTransaction::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}

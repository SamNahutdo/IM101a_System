<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BorrowingItem extends Model
{
    public $timestamps = false;
    protected $table = 'borrowing_items';

    protected $fillable = [
        'transaction_id',
        'equipment_id',
        'checkout_condition',
        'return_condition',
        'return_inspected_by',
        'returned_at',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'returned_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(BorrowingTransaction::class, 'transaction_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'return_inspected_by');
    }

    public function damageReport(): HasOne
    {
        return $this->hasOne(DamageReport::class, 'borrowing_item_id');
    }
}

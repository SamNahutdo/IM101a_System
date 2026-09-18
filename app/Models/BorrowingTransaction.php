<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class BorrowingTransaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'reservation_id',
        'borrower_id',
        'team_id',
        'processed_by',
        'checkout_time',
        'expected_return_time',
        'actual_return_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'checkout_time' => 'datetime',
            'expected_return_time' => 'datetime',
            'actual_return_time' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BorrowingItem::class, 'transaction_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return is_null($this->actual_return_time) && Carbon::now()->greaterThan($this->expected_return_time);
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return (int) Carbon::now()->diffInDays($this->expected_return_time);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'Active')
                     ->where('expected_return_time', '<', Carbon::now())
                     ->whereNull('actual_return_time');
    }
}

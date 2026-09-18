<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    public $timestamps = false;
    protected $table = 'reservation_items';

    protected $fillable = [
        'reservation_id',
        'equipment_id',
        'requested_quantity',
        'approved_quantity',
        'item_status',
        'notes',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}

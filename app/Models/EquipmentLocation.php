<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentLocation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'building',
        'room',
        'shelf_bin',
        'description',
    ];

    public function getFullLocationAttribute(): string
    {
        $loc = "{$this->building} - {$this->room}";
        if ($this->shelf_bin) {
            $loc .= " ({$this->shelf_bin})";
        }
        return $loc;
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'location_id');
    }
}

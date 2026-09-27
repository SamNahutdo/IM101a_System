<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'reported_by',
        'maintenance_type',
        'description',
        'scheduled_date',
        'completed_date',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed_date' => 'date',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}

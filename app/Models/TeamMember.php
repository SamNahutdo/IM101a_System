<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $table = 'team_members';

    protected $fillable = [
        'team_id',
        'athlete_id',
        'joined_at',
        'position',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}

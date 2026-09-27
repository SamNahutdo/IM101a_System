<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'coach_id',
        'team_name',
        'school_year',
        'status',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    // Many-to-Many Relationship 1: Teams <-> Athletes via team_members
    public function athletes()
    {
        return $this->belongsToMany(Athlete::class, 'team_members')
            ->withPivot(['id', 'joined_at', 'position', 'status'])
            ->withTimestamps();
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }
}

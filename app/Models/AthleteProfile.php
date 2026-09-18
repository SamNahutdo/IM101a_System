<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AthleteProfile extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_clearance_status',
        'year_level',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members', 'athlete_id', 'team_id')
                    ->withPivot('jersey_number', 'position', 'membership_status', 'joined_at', 'left_at');
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'athlete_id');
    }

    public function equipmentAssignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class, 'athlete_id');
    }
}

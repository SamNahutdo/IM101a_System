<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'sport',
        'coach_id',
        'gender_category',
        'status',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(CoachProfile::class, 'coach_id');
    }

    public function athletes(): BelongsToMany
    {
        return $this->belongsToMany(AthleteProfile::class, 'team_members', 'team_id', 'athlete_id')
                    ->withPivot('jersey_number', 'position', 'membership_status', 'joined_at', 'left_at');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }
}

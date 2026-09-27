<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'first_name',
        'last_name',
        'department',
        'year_level',
        'contact_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Many-to-Many Relationship 1: Athletes <-> Teams via team_members
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot(['id', 'joined_at', 'position', 'status'])
            ->withTimestamps();
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function borrowingTransactions()
    {
        return $this->hasMany(BorrowingTransaction::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}

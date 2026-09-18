<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot('is_primary', 'assigned_at');
    }

    public function athleteProfile(): HasOne
    {
        return $this->hasOne(AthleteProfile::class);
    }

    public function coachProfile(): HasOne
    {
        return $this->hasOne(CoachProfile::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'requester_id');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class, 'borrower_id');
    }

    public function processedBorrowings(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class, 'processed_by');
    }

    public function hasRole(string|array $roleNames): bool
    {
        $roleNames = is_array($roleNames) ? $roleNames : [$roleNames];
        return $this->roles->contains(fn ($r) => in_array($r->name, $roleNames));
    }

    public function getPrimaryRole(): ?Role
    {
        return $this->roles->firstWhere('pivot.is_primary', true) ?? $this->roles->first();
    }
}

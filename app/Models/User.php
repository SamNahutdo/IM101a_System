<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'username',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function athlete()
    {
        return $this->hasOne(Athlete::class);
    }

    public function coach()
    {
        return $this->hasOne(Coach::class);
    }

    public function approvedBorrowings()
    {
        return $this->hasMany(BorrowingTransaction::class, 'approved_by');
    }

    public function maintenanceReports()
    {
        return $this->hasMany(MaintenanceRecord::class, 'reported_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Role helper checks
    public function isAdmin(): bool
    {
        return strtolower($this->role->name ?? '') === 'admin';
    }

    public function isStaff(): bool
    {
        return strtolower($this->role->name ?? '') === 'staff';
    }

    public function isCoach(): bool
    {
        return strtolower($this->role->name ?? '') === 'coach';
    }

    public function isStudent(): bool
    {
        return strtolower($this->role->name ?? '') === 'student';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->athlete) {
            return $this->athlete->first_name . ' ' . $this->athlete->last_name;
        }
        if ($this->coach) {
            return $this->coach->first_name . ' ' . $this->coach->last_name;
        }
        return $this->username;
    }
}

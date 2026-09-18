<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    public $timestamps = false;
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'is_primary',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'assigned_at' => 'datetime',
        ];
    }
}

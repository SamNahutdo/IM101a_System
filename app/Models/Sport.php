<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_name',
        'description',
        'status',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}

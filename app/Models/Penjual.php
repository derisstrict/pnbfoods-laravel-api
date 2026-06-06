<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Penjual extends Authenticatable
{ 
    use HasApiTokens;

    protected $table = 'penjual';

    protected $fillable = [
        'email',
        'nama',
        'role',
        'password',
        'foto_profile',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_profile) {
            return null;
        }
        return url('storage/' . $this->foto_profile);
    }
}
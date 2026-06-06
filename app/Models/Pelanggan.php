<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;  
use Laravel\Sanctum\HasApiTokens;

class Pelanggan extends Authenticatable
{ 
    use HasApiTokens;
    
    protected $table = 'pelanggan';

    protected $fillable = [
        'nim',
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

    public function orderan(): HasMany
    {
        return $this->hasMany(Orderan::class);
    }

    public function favorit(): HasMany
    {
        return $this->hasMany(Favorit::class);
    }
}
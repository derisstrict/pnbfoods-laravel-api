<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Penjual extends Authenticatable
{ 
    use HasApiTokens;

    protected $table = 'penjual';

    const ROLE = 'penjual';

    protected $fillable = [
        'kantin_id',
        'email',
        'nama',
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

    public function kantin(): BelongsTo
    {
        return $this->belongsTo(Kantin::class);
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
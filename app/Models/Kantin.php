<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantin extends Model
{
    protected $table = 'kantin';

    protected $fillable = [
        'nama_kantin',
        'foto_kantin',
        'kategori',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_kantin) {
            return null;
        }
        return url('storage/' . $this->foto_kantin);
    }
}

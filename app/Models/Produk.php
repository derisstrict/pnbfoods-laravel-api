<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'foto_produk',
        'deskripsi_produk',
        'kategori_produk',
        'harga_produk',
        'stok',
    ];

    protected $casts = [
        'harga_produk' => 'decimal:2',
        'stok' => 'integer',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_produk) {
            return null;
        }
        return url('storage/' . $this->foto_produk);
    }
}

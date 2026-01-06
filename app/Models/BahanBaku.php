<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $fillable = [
        'nama_bahan',
        'stok',
        'satuan'
    ];

    public function produks()
{
    return $this->belongsToMany(Produk::class, 'produk_bahan_bakus')
        ->withPivot('qty');
}
}
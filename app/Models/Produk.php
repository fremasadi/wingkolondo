<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['nama_produk', 'stok', 'harga'];

    public function detailReturs()
    {
        return $this->hasMany(DetailRetur::class);
    }

    public function bahanBakus()
    {
        return $this->belongsToMany(BahanBaku::class, 'produk_bahan_bakus')->withPivot('qty');
    }
}

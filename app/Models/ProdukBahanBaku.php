<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukBahanBaku extends Model
{
    protected $fillable = [
        'produk_id',
        'bahan_baku_id',
        'qty',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }
}
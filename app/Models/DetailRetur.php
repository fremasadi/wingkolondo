<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRetur extends Model
{
    protected $fillable = [
        'retur_id',
        'produk_id',
        'qty',
        'harga',
        'subtotal'
    ];

    public function retur()
    {
        return $this->belongsTo(Retur::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
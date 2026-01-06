<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduksi extends Model
{
    protected $table = 'detail_produksis';

    protected $fillable = [
        'produksi_id',
        'produk_id',
        'qty'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Detail milik satu produksi
    public function produksi()
    {
        return $this->belongsTo(Produksi::class);
    }

    // Detail menghasilkan satu produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
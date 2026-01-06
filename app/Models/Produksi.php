<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $fillable = [
        'tanggal_produksi',
        'status',
        'catatan'
    ];

    public function details()
    {
        return $this->hasMany(DetailProduksi::class);
    }
}
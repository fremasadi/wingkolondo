<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembuanganStok extends Model
{
    protected $table = 'pembuangan_stoks';

    protected $fillable = [
        'produk_id',
        'qty',
        'tanggal_buang',
        'keterangan',
        'metode',
    ];

    protected $casts = [
        'tanggal_buang' => 'date',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

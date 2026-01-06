<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $fillable = [
        'toko_id',
        'pesanan_id',
        'total_tagihan',
        'sisa_tagihan',
        'jatuh_tempo',
        'status'
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
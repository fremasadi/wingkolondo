<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    protected $fillable = [
        'pesanan_id',
        'kurir_id',
        'tanggal_kirim',
        'status_pengiriman',
        'catatan'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Distribusi milik satu pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    // Distribusi memiliki satu kurir (user)
    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }

    public function retur()
{
    return $this->hasOne(Retur::class);
}
}
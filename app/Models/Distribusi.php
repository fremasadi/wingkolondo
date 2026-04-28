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
        'catatan',
        'delivered_at',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_photo',
        'delivery_note',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'delivered_at' => 'datetime',
        'approved_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function markAsRetur(): void
    {
        if ($this->status_pengiriman === 'retur') {
            return;
        }

        $this->update([
            'status_pengiriman' => 'retur',
        ]);
    }
}

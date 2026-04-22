<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $fillable = [
        'distribusi_id',
        'kurir_id',
        'tanggal_retur',
        'status',
        'alasan',
        'refund_method',
        'total_retur',
        'total_refund',
        'tanggal_pengambilan',
        'picked_up_at',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_photo',
        'pickup_note',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_retur' => 'date',
        'tanggal_pengambilan' => 'date',
        'picked_up_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function distribusi()
    {
        return $this->belongsTo(Distribusi::class);
    }

    public function details()
    {
        return $this->hasMany(DetailRetur::class);
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

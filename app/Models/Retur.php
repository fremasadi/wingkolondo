<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $fillable = [
        'distribusi_id',
        'tanggal_retur',
        'alasan',
        'total_retur'
    ];

    public function distribusi()
    {
        return $this->belongsTo(Distribusi::class);
    }

    public function details()
    {
        return $this->hasMany(DetailRetur::class);
    }
}
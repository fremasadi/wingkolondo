<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'total_retur' => 'decimal:2',
        'total_refund' => 'decimal:2',
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

    public function complete(?int $approvedBy = null): void
    {
        if ($this->status === 'selesai') {
            return;
        }

        DB::transaction(function () use ($approvedBy) {
            $this->loadMissing('distribusi.pesanan.piutang');

            $pesanan = $this->distribusi?->pesanan;

            if ($pesanan?->metode_pembayaran === 'tempo' && $pesanan->piutang) {
                $pesanan->piutang->applyReturAdjustment($this->refund_total);
            }

            $this->distribusi?->markAsRetur();

            $this->update([
                'status' => 'selesai',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);
        });
    }

    public function getRefundTotalAttribute(): float
    {
        return (float) ($this->total_refund ?: $this->total_retur ?: 0);
    }
}

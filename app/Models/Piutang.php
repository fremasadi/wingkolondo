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

    public function applyReturAdjustment(float $refundAmount): void
    {
        if ($refundAmount <= 0) {
            return;
        }

        $totalTagihanBaru = max(0, (float) $this->total_tagihan - $refundAmount);
        $totalSudahDibayar = max(0, (float) $this->total_tagihan - (float) $this->sisa_tagihan);
        $sisaTagihanBaru = max(0, $totalTagihanBaru - $totalSudahDibayar);

        $this->update([
            'total_tagihan' => $totalTagihanBaru,
            'sisa_tagihan' => $sisaTagihanBaru,
            'status' => $sisaTagihanBaru == 0 ? 'lunas' : 'belum_lunas',
        ]);
    }
}

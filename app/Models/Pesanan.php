<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPesanan;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    protected $fillable = ['order_code', 'toko_id', 'tanggal_pesanan', 'tanggal_kirim', 'status_pesanan','metode_pembayaran', 'total_harga'];

    protected static function booted()
    {
        static::creating(function (Pesanan $pesanan) {
            if ($pesanan->order_code) {
                return;
            }

            do {
                $date = $pesanan->tanggal_pesanan
                    ? date('Ymd', strtotime($pesanan->tanggal_pesanan))
                    : date('Ymd');

                $orderCode = 'WL-' . $date . '-' . Str::upper(Str::random(6));
            } while (static::where('order_code', $orderCode)->exists());

            $pesanan->order_code = $orderCode;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Pesanan milik satu toko
    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    // Pesanan memiliki banyak detail (produk)
    public function details()
    {
        return $this->hasMany(DetailPesanan::class);
    }
    /*
    |--------------------------------------------------------------------------
    | HELPER (OPTIONAL)
    |--------------------------------------------------------------------------
    */

    // Cek apakah pesanan masih bisa diedit
    public function isEditable()
    {
        return in_array($this->status_pesanan, ['draft', 'dikonfirmasi']);
    }

    // Format total harga
    public function getTotalHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function updateTotalHarga()
    {
        $total = $this->details()->sum('subtotal');

        $this->update([
            'total_harga' => $total,
        ]);
    }

    public function distribusi()
    {
        return $this->hasOne(Distribusi::class);
    }

    public function returs()
    {
        return $this->hasManyThrough(Retur::class, Distribusi::class);
    }
}

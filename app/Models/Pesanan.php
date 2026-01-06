<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPesanan;

class Pesanan extends Model
{
    protected $fillable = ['toko_id', 'tanggal_pesanan', 'tanggal_kirim', 'status_pesanan','metode_pembayaran', 'total_harga'];

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
}

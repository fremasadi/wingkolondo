<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Produk extends Model
{
    protected $fillable = ['nama_produk', 'stok', 'harga'];

    public function pembuanganStoks()
    {
        return $this->hasMany(PembuanganStok::class);
    }

    public function detailReturs()
    {
        return $this->hasMany(DetailRetur::class);
    }

    public function bahanBakus()
    {
        return $this->belongsToMany(BahanBaku::class, 'produk_bahan_bakus')->withPivot('qty');
    }

    // Relasi ke detail produksi
    public function detailProduksis()
    {
        return $this->hasMany(DetailProduksi::class);
    }

    // Relasi ke produksi terakhir yang selesai
    public function latestProduksi()
    {
        return $this->hasOneThrough(
            Produksi::class,
            DetailProduksi::class,
            'produk_id',   // FK on detail_produksis
            'id',          // FK on produksis
            'id',          // local key on produks
            'produksi_id'  // local key on detail_produksis
        )->where('produksis.status', 'selesai')
         ->orderByDesc('produksis.tanggal_produksi');
    }

    /**
     * Tanggal kadaluarsa = tanggal produksi terakhir (selesai) + 30 hari
     * Jika belum pernah diproduksi, return null.
     */
    public function getTanggalKadaluarsaAttribute(): ?Carbon
    {
        $lastProduksi = $this->detailProduksis()
            ->join('produksis', 'produksis.id', '=', 'detail_produksis.produksi_id')
            ->where('produksis.status', 'selesai')
            ->orderByDesc('produksis.tanggal_produksi')
            ->value('produksis.tanggal_produksi');

        return $lastProduksi ? Carbon::parse($lastProduksi)->addDays(30) : null;
    }

    public function getKadaluarsaStatusAttribute(): string
    {
        $exp = $this->tanggal_kadaluarsa;
        if (!$exp) return 'unknown';
        if ($exp->isPast()) return 'expired';
        if ($exp->diffInDays(now()) <= 7) return 'warning';
        return 'fresh';
    }

    // Helper method untuk cek apakah bisa dihapus
    public function canBeDeleted()
    {
        return $this->detailProduksis()->count() === 0
            && $this->detailReturs()->count() === 0;
    }
}

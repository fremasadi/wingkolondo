<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['nama_produk', 'stok', 'harga'];

    public function detailReturs()
    {
        return $this->hasMany(DetailRetur::class);
    }

    public function bahanBakus()
    {
        return $this->belongsToMany(BahanBaku::class, 'produk_bahan_bakus')->withPivot('qty');
    }

    // Tambahkan relasi ke detail produksi
    public function detailProduksis()
    {
        return $this->hasMany(DetailProduksi::class);
    }

    // Helper method untuk cek apakah bisa dihapus
    public function canBeDeleted()
    {
        return $this->detailProduksis()->count() === 0
            && $this->detailReturs()->count() === 0;
    }
}

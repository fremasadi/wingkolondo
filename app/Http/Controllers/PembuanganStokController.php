<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DetailProduksi;
use App\Models\PembuanganStok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembuanganStokController extends Controller
{
    /**
     * Halaman daftar pembuangan stok
     */
    public function index()
    {
        $pembuangans = PembuanganStok::with('produk')
            ->latest('tanggal_buang')
            ->paginate(20);

        return view('pembuangan_stok.index', compact('pembuangans'));
    }

    /**
     * Buang stok kadaluarsa secara manual (dipicu dari tombol di UI)
     */
    public function buangManual(Request $request, Produk $produk)
    {
        $request->validate([
            'qty' => 'required|integer|min:1|max:' . $produk->stok,
        ], [
            'qty.max' => 'Qty tidak boleh melebihi stok yang tersedia (' . $produk->stok . ' pcs).',
        ]);

        DB::transaction(function () use ($request, $produk) {
            $exp = $produk->tanggal_kadaluarsa;

            PembuanganStok::create([
                'produk_id'     => $produk->id,
                'qty'           => $request->qty,
                'tanggal_buang' => today(),
                'keterangan'    => 'Manual - ' . ($exp ? 'Kadaluarsa ' . $exp->format('d/m/Y') : 'Tidak ada info kadaluarsa'),
                'metode'        => 'manual',
            ]);

            $produk->decrement('stok', $request->qty);
        });

        return redirect()->back()->with('success', "Berhasil membuang {$request->qty} pcs stok kadaluarsa produk \"{$produk->nama_produk}\".");
    }

    /**
     * Buang stok semua produk yang kadaluarsa secara otomatis
     * Dipicu dari tombol "Jalankan Otomatis" atau artisan command
     */
    public function buangOtomatis()
    {
        $count = 0;
        $totalQty = 0;

        // Ambil semua produk yang pernah diproduksi
        $produks = Produk::where('stok', '>', 0)->get();

        DB::transaction(function () use ($produks, &$count, &$totalQty) {
            foreach ($produks as $produk) {
                $exp = $produk->tanggal_kadaluarsa;

                // Skip jika belum pernah diproduksi atau belum kadaluarsa
                if (!$exp || !$exp->isPast()) {
                    continue;
                }

                // Cek apakah hari ini sudah pernah dibuang otomatis untuk produk ini
                $sudahDibuang = PembuanganStok::where('produk_id', $produk->id)
                    ->where('metode', 'otomatis')
                    ->whereDate('tanggal_buang', today())
                    ->exists();

                if ($sudahDibuang) {
                    continue;
                }

                $qtyBuang = $produk->stok;

                PembuanganStok::create([
                    'produk_id'     => $produk->id,
                    'qty'           => $qtyBuang,
                    'tanggal_buang' => today(),
                    'keterangan'    => 'Otomatis - Kadaluarsa ' . $exp->format('d/m/Y'),
                    'metode'        => 'otomatis',
                ]);

                $produk->decrement('stok', $qtyBuang);

                $count++;
                $totalQty += $qtyBuang;
            }
        });

        if ($count === 0) {
            return redirect()->back()->with('info', 'Tidak ada stok kadaluarsa yang perlu dibuang saat ini.');
        }

        return redirect()->back()->with('success', "Berhasil membuang stok otomatis: {$count} produk ({$totalQty} pcs) dibuang karena kadaluarsa.");
    }
}

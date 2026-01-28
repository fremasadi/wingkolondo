<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produksi;
use App\Models\DetailProduksi;
use App\Models\Produk;

class ProduksiController extends Controller
{
    public function index()
    {
        $produksis = Produksi::with('details.produk')->latest()->get();

        return view('produksi.index', compact('produksis'));
    }

    public function create()
    {
        $produks = Produk::all();
        return view('produksi.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_produksi' => 'required|date',
            'produk_id.*' => 'required|exists:produks,id',
            'qty.*' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $produksi = Produksi::create([
                'tanggal_produksi' => $request->tanggal_produksi,
                'status' => 'diproduksi',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->produk_id as $i => $produkId) {
                DetailProduksi::create([
                    'produksi_id' => $produksi->id,
                    'produk_id' => $produkId,
                    'qty' => $request->qty[$i],
                ]);
            }
        });

        return redirect()->route('produksis.index')->with('success', 'Produksi berhasil ditambahkan');
    }

    public function show(Produksi $produksi)
    {
        $produksi->load('details.produk');
        return view('produksi.show', compact('produksi'));
    }

    public function selesai($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $produksi = Produksi::with('details.produk.bahanBakus')->lockForUpdate()->findOrFail($id);

                // ❌ Cegah double proses
                if ($produksi->status === 'selesai') {
                    throw new \Exception('Produksi sudah diselesaikan sebelumnya.');
                }

                // 🔍 Hitung total kebutuhan bahan baku dulu
                $kebutuhanBahan = [];
                foreach ($produksi->details as $detail) {
                    foreach ($detail->produk->bahanBakus as $bahan) {
                        $kebutuhan = $bahan->pivot->qty * $detail->qty;

                        if (isset($kebutuhanBahan[$bahan->id])) {
                            $kebutuhanBahan[$bahan->id]['kebutuhan'] += $kebutuhan;
                        } else {
                            $kebutuhanBahan[$bahan->id] = [
                                'nama' => $bahan->nama_bahan,
                                'stok' => $bahan->stok,
                                'kebutuhan' => $kebutuhan,
                            ];
                        }
                    }
                }

                // ❌ Validasi stok bahan baku
                $bahanKurang = [];
                foreach ($kebutuhanBahan as $bahan) {
                    if ($bahan['stok'] < $bahan['kebutuhan']) {
                        $kurang = $bahan['kebutuhan'] - $bahan['stok'];
                        $bahanKurang[] = "{$bahan['nama']} (kurang: {$kurang})";
                    }
                }

                if (count($bahanKurang) > 0) {
                    throw new \Exception('Stok bahan baku tidak mencukupi: ' . implode(', ', $bahanKurang));
                }

                // ✅ Proses jika stok cukup
                foreach ($produksi->details as $detail) {
                    $produk = $detail->produk;
                    $qtyProduksi = $detail->qty;

                    // ➕ Tambah stok produk
                    $produk->increment('stok', $qtyProduksi);

                    // ➖ Kurangi bahan baku
                    foreach ($produk->bahanBakus as $bahan) {
                        $kebutuhan = $bahan->pivot->qty * $qtyProduksi;
                        $bahan->decrement('stok', $kebutuhan);
                    }
                }

                // ✅ Update status
                $produksi->update(['status' => 'selesai']);
            });

            return back()->with('success', 'Produksi berhasil diselesaikan! Stok produk bertambah dan bahan baku berkurang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

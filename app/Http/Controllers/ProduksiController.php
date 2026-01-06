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
        DB::transaction(function () use ($id) {
            $produksi = Produksi::with('details.produk.bahanBakus')->lockForUpdate()->findOrFail($id);

            // ❌ Cegah double proses
            if ($produksi->status === 'selesai') {
                abort(400, 'Produksi sudah diselesaikan');
            }

            // 1️⃣ Tambah stok produk
            foreach ($produksi->details as $detail) {
                $produk = $detail->produk;
                $qtyProduksi = $detail->qty;

                // ➕ stok produk
                $produk->increment('stok', $qtyProduksi);

                // 2️⃣ Kurangi bahan baku
                foreach ($produk->bahanBakus as $bahan) {
                    $kebutuhan = $bahan->pivot->qty * $qtyProduksi;

                    if ($bahan->stok < $kebutuhan) {
                        abort(400, 'Stok bahan baku tidak cukup');
                    }

                    $bahan->decrement('stok', $kebutuhan);
                }
            }

            // 3️⃣ Update status produksi
            $produksi->update([
                'status' => 'selesai',
            ]);
        });

        return back()->with('success', 'Produksi berhasil diselesaikan');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\ProdukBahanBaku;
use DB;

class ProdukController extends Controller
{
    public function index()
{
    $produks = Produk::withCount(['detailProduksis', 'detailReturs'])->latest()->get();
    return view('produk.index', compact('produks'));
}

    public function create()
    {
        return view('produk.create', [
            'bahanBakus' => BahanBaku::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $produk = Produk::create($request->only('nama_produk', 'stok', 'harga'));

            foreach ($request->bahan ?? [] as $bahanId => $qty) {
                if ($qty > 0) {
                    ProdukBahanBaku::create([
                        'produk_id' => $produk->id,
                        'bahan_baku_id' => $bahanId,
                        'qty' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('produks.index')->with('success', 'Produk & resep berhasil ditambahkan');
    }

    public function edit(Produk $produk)
    {
        return view('produk.edit', [
            'produk' => $produk,
            'bahanBakus' => BahanBaku::all(),
            'resep' => $produk->bahanBakus->pluck('pivot.qty', 'id'),
        ]);
    }

    public function update(Request $request, Produk $produk)
    {
        DB::transaction(function () use ($request, $produk) {
            $produk->update($request->only('nama_produk', 'stok', 'harga'));

            // hapus resep lama
            $produk->bahanBakus()->detach();

            // simpan ulang
            foreach ($request->bahan ?? [] as $bahanId => $qty) {
                if ($qty > 0) {
                    $produk->bahanBakus()->attach($bahanId, [
                        'qty' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('produks.index')->with('success', 'Produk & resep berhasil diperbarui');
    }

    public function destroy(Produk $produk)
    {
        if (!$produk->canBeDeleted()) {
            return redirect()->route('produks.index')
                ->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam produksi atau retur.');
        }

        $produk->delete();

        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus');
    }

    public function show(Produk $produk)
{
    $produk->load('bahanBakus'); // eager load relasi
    return view('produk.show', compact('produk'));
}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Retur;
use App\Models\Distribusi;
use App\Models\DetailRetur;
use App\Models\Produk;

class ReturController extends Controller
{
    public function index()
    {
        $returs = Retur::with('distribusi.pesanan.toko')
            ->latest()
            ->get();

        return view('retur.index', compact('returs'));
    }

    public function create()
    {
        $distribusis = Distribusi::where('status_pengiriman', 'selesai')
            ->doesntHave('retur')
            ->with('pesanan.toko')
            ->get();

        return view('retur.create', compact('distribusis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distribusi_id' => 'required|exists:distribusis,id',
            'tanggal_retur' => 'required|date',
            'produk_id.*' => 'required|exists:produks,id',
            'qty.*' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $distribusi = Distribusi::with('pesanan.details')->findOrFail($request->distribusi_id);

            if ($distribusi->retur) {
                abort(400, 'Distribusi sudah diretur');
            }

            $retur = Retur::create([
                'distribusi_id' => $distribusi->id,
                'tanggal_retur' => $request->tanggal_retur,
                'alasan' => $request->alasan,
                'total_retur' => 0
            ]);

            $total = 0;

            foreach ($request->produk_id as $i => $produkId) {
                $qty = $request->qty[$i];

                $detailPesanan = $distribusi->pesanan
                    ->details()
                    ->where('produk_id', $produkId)
                    ->firstOrFail();

                $subtotal = $qty * $detailPesanan->harga;

                DetailRetur::create([
                    'retur_id' => $retur->id,
                    'produk_id' => $produkId,
                    'qty' => $qty,
                    'harga' => $detailPesanan->harga,
                    'subtotal' => $subtotal
                ]);

                Produk::find($produkId)->increment('stok', $qty);

                $total += $subtotal;
            }

            $retur->update(['total_retur' => $total]);

            $distribusi->update(['status_pengiriman' => 'retur']);
            $distribusi->pesanan->update([
                'status_pesanan' => 'retur',
                'total_harga' => $distribusi->pesanan->total_harga - $total
            ]);
        });

        return redirect()->route('returs.index')->with('success', 'Retur berhasil disimpan');
    }

    public function show(Retur $retur)
    {
        $retur->load('details.produk', 'distribusi.pesanan.toko');

        return view('retur.show', compact('retur'));
    }
}
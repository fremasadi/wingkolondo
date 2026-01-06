<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Toko;
use App\Models\DetailPesanan;
use App\Models\Produk;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with('toko')->latest()->get();
        return view('pesanan.index', compact('pesanans'));
    }

    public function create()
    {
        return view('pesanan.create', [
            'tokos' => Toko::orderBy('nama_toko')->get(),
            'produks' => Produk::orderBy('nama_produk')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'tanggal_pesanan' => 'required|date',
            'produk_id.*' => 'required|exists:produks,id',
            'qty.*' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:cash,transfer,tempo'

        ]);

        // 1. Simpan header pesanan
        $pesanan = Pesanan::create([
            'toko_id' => $request->toko_id,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'tanggal_kirim' => $request->tanggal_kirim,
            'status_pesanan' => 'diproses',
            'total_harga' => 0,
        ]);

        // 2. Simpan detail pesanan
        foreach ($request->produk_id as $index => $produkId) {
            $produk = Produk::findOrFail($produkId);

            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produkId,
                'qty' => $request->qty[$index],
                'harga' => $produk->harga,
                'subtotal' => $produk->harga * $request->qty[$index],
            ]);
        }

        // 3. Update total harga
        $pesanan->updateTotalHarga();

        return redirect()->route('pesanans.index')->with('success', 'Pesanan berhasil disimpan');
    }

    public function edit(Pesanan $pesanan)
    {
        return view('pesanan.edit', [
            'pesanan' => $pesanan->load('details.produk'),
            'tokos' => Toko::orderBy('nama_toko')->get(),
            'produks' => Produk::orderBy('nama_produk')->get(),
        ]);
    }
    public function update(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'toko_id' => 'required',
            'tanggal_pesanan' => 'required|date',
            'produk_id.*' => 'required|exists:produks,id',
            'qty.*' => 'required|integer|min:1',
        ]);

        // Update header
        $pesanan->update([
            'toko_id' => $request->toko_id,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'tanggal_kirim' => $request->tanggal_kirim,
        ]);

        // Hapus detail lama
        $pesanan->details()->delete();

        // Simpan detail baru
        foreach ($request->produk_id as $index => $produkId) {
            $produk = Produk::findOrFail($produkId);

            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produkId,
                'qty' => $request->qty[$index],
                'harga' => $produk->harga,
                'subtotal' => $produk->harga * $request->qty[$index],
            ]);
        }

        $pesanan->updateTotalHarga();

        return redirect()->route('pesanans.index')->with('success', 'Pesanan berhasil diperbarui');
    }

    public function destroy(Pesanan $pesanan)
    {
        if (!$pesanan->isEditable()) {
            return redirect()->back()->with('error', 'Pesanan tidak bisa dihapus');
        }

        $pesanan->delete();

        return redirect()->route('pesanans.index')->with('success', 'Pesanan berhasil dihapus');
    }
}

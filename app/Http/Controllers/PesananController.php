<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Toko;
use App\Models\DetailPesanan;
use App\Models\Produk;
use App\Models\Distribusi;
use App\Models\User;
use App\Models\Piutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with(['toko', 'distribusi.kurir'])->latest()->get();
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

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Pesanan berhasil disimpan. Silakan tambah distribusi jika diperlukan.');
    }

    public function edit(Pesanan $pesanan)
    {
        return view('pesanan.edit', [
            'pesanan' => $pesanan->load(['details.produk', 'distribusi']),
            'tokos' => Toko::orderBy('nama_toko')->get(),
            'produks' => Produk::orderBy('nama_produk')->get(),
            'kurirs' => User::where('role', 'kurir')->get(),
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

    public function storeDistribusi(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'kurir_id' => 'nullable|exists:users,id',
            'tanggal_kirim' => 'required|date',
        ]);

        Distribusi::create([
            'pesanan_id' => $pesanan->id,
            'kurir_id' => $request->kurir_id,
            'tanggal_kirim' => $request->tanggal_kirim,
            'status_pengiriman' => 'pending',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Distribusi berhasil dibuat');
    }

    public function updateDistribusi(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'kurir_id' => 'nullable|exists:users,id',
            'tanggal_kirim' => 'required|date',
            'status_pengiriman' => 'required',
        ]);

        $pesanan->distribusi->update($request->only(['kurir_id', 'tanggal_kirim', 'status_pengiriman', 'catatan']));

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Distribusi berhasil diperbarui');
    }

    public function selesaiDistribusi(Pesanan $pesanan)
    {
        $distribusi = $pesanan->distribusi;

        if (!$distribusi) {
            return back()->with('error', 'Distribusi tidak ditemukan');
        }

        if ($distribusi->status_pengiriman === 'selesai') {
            return back()->with('success', 'Distribusi sudah selesai');
        }

        DB::transaction(function () use ($distribusi, $pesanan) {
            $distribusi->update([
                'status_pengiriman' => 'selesai',
            ]);

            if ($pesanan->metode_pembayaran === 'tempo') {
                Piutang::firstOrCreate(
                    ['pesanan_id' => $pesanan->id],
                    [
                        'toko_id' => $pesanan->toko_id,
                        'total_tagihan' => $pesanan->total_harga,
                        'sisa_tagihan' => $pesanan->total_harga,
                        'status' => 'belum_lunas',
                    ],
                );
            }
        });

        // Redirect based on referer
        $referer = request()->headers->get('referer');
        if (str_contains($referer, 'view=distribusi')) {
            return redirect()->route('pesanans.index', ['view' => 'distribusi'])->with('success', 'Distribusi selesai & piutang diproses');
        }

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Distribusi selesai & piutang diproses');
    }

    public function destroyDistribusi(Pesanan $pesanan)
    {
        if ($pesanan->distribusi) {
            $pesanan->distribusi->delete();
        }

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Distribusi berhasil dihapus');
    }
}

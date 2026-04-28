<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Distribusi;
use App\Models\Pesanan;
use App\Models\Piutang;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\User;
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
            'tanggal_kirim' => 'required|date|after_or_equal:tanggal_pesanan',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|exists:produks,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:cash,transfer,tempo',

        ], [
            'toko_id.required' => 'Toko wajib dipilih.',
            'toko_id.exists' => 'Toko yang dipilih tidak valid.',
            'tanggal_pesanan.required' => 'Tanggal pesanan wajib diisi.',
            'tanggal_pesanan.date' => 'Tanggal pesanan tidak valid.',
            'tanggal_kirim.required' => 'Tanggal kirim wajib diisi.',
            'tanggal_kirim.date' => 'Tanggal kirim tidak valid.',
            'tanggal_kirim.after_or_equal' => 'Tanggal kirim tidak boleh sebelum tanggal pesanan.',
            'produk_id.required' => 'Minimal harus ada 1 produk.',
            'produk_id.array' => 'Format produk tidak valid.',
            'produk_id.min' => 'Minimal harus ada 1 produk.',
            'produk_id.*.required' => 'Produk wajib dipilih.',
            'produk_id.*.exists' => 'Produk yang dipilih tidak valid.',
            'qty.required' => 'Qty produk wajib diisi.',
            'qty.array' => 'Format qty produk tidak valid.',
            'qty.min' => 'Minimal harus ada 1 qty produk.',
            'qty.*.required' => 'Qty produk wajib diisi.',
            'qty.*.integer' => 'Qty produk harus berupa angka bulat.',
            'qty.*.min' => 'Qty produk minimal 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran yang dipilih tidak valid.',
        ]);

        // 1. Simpan header pesanan
        $pesanan = Pesanan::create([
            'toko_id' => $request->toko_id,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'tanggal_kirim' => $request->tanggal_kirim,
            'status_pesanan' => 'diproses',
            'metode_pembayaran' => $request->metode_pembayaran,
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
            'toko_id' => 'required|exists:tokos,id',
            'tanggal_pesanan' => 'required|date',
            'tanggal_kirim' => 'required|date|after_or_equal:tanggal_pesanan',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|exists:produks,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:cash,transfer,tempo',
            'status_pesanan' => 'required|in:draft,dikonfirmasi,diproses,dikirim,selesai,batal',
        ], [
            'toko_id.required' => 'Toko wajib dipilih.',
            'toko_id.exists' => 'Toko yang dipilih tidak valid.',
            'tanggal_pesanan.required' => 'Tanggal pesanan wajib diisi.',
            'tanggal_pesanan.date' => 'Tanggal pesanan tidak valid.',
            'tanggal_kirim.required' => 'Tanggal kirim wajib diisi.',
            'tanggal_kirim.date' => 'Tanggal kirim tidak valid.',
            'tanggal_kirim.after_or_equal' => 'Tanggal kirim tidak boleh sebelum tanggal pesanan.',
            'produk_id.required' => 'Minimal harus ada 1 produk.',
            'produk_id.array' => 'Format produk tidak valid.',
            'produk_id.min' => 'Minimal harus ada 1 produk.',
            'produk_id.*.required' => 'Produk wajib dipilih.',
            'produk_id.*.exists' => 'Produk yang dipilih tidak valid.',
            'qty.required' => 'Qty produk wajib diisi.',
            'qty.array' => 'Format qty produk tidak valid.',
            'qty.min' => 'Minimal harus ada 1 qty produk.',
            'qty.*.required' => 'Qty produk wajib diisi.',
            'qty.*.integer' => 'Qty produk harus berupa angka bulat.',
            'qty.*.min' => 'Qty produk minimal 1.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran yang dipilih tidak valid.',
            'status_pesanan.required' => 'Status pesanan wajib dipilih.',
            'status_pesanan.in' => 'Status pesanan yang dipilih tidak valid.',
        ]);

        // Update header
        $pesanan->update([
            'toko_id' => $request->toko_id,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'tanggal_kirim' => $request->tanggal_kirim,
            'status_pesanan' => $request->status_pesanan,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        if ($pesanan->distribusi) {
            $pesanan->distribusi->update([
                'tanggal_kirim' => $request->tanggal_kirim,
            ]);
        }

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

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Pesanan berhasil diperbarui');
    }

    public function destroy(Pesanan $pesanan)
    {
        if (! $pesanan->isEditable()) {
            return redirect()->back()->with('error', 'Pesanan tidak bisa dihapus');
        }

        $pesanan->delete();

        return redirect()->route('pesanans.index')->with('success', 'Pesanan berhasil dihapus');
    }

    public function storeDistribusi(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'kurir_id' => 'nullable|exists:users,id',
        ]);

        if (! $pesanan->tanggal_kirim) {
            return redirect()
                ->route('pesanans.edit', $pesanan)
                ->with('error', 'Tanggal kirim pada pesanan wajib diisi sebelum membuat distribusi.');
        }

        Distribusi::create([
            'pesanan_id' => $pesanan->id,
            'kurir_id' => $request->kurir_id,
            'tanggal_kirim' => $pesanan->tanggal_kirim,
            'status_pengiriman' => 'pending',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Distribusi berhasil dibuat');
    }

    public function updateDistribusi(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'kurir_id' => 'nullable|exists:users,id',
            'status_pengiriman' => 'required|in:pending,dikirim,terkirim,selesai',
        ]);

        if (! $pesanan->tanggal_kirim) {
            return redirect()
                ->route('pesanans.edit', $pesanan)
                ->with('error', 'Tanggal kirim pada pesanan wajib diisi sebelum memperbarui distribusi.');
        }

        $pesanan->distribusi->update([
            'kurir_id' => $request->kurir_id,
            'tanggal_kirim' => $pesanan->tanggal_kirim,
            'status_pengiriman' => $request->status_pengiriman,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pesanans.edit', $pesanan)->with('success', 'Distribusi berhasil diperbarui');
    }

    public function selesaiDistribusi(Pesanan $pesanan)
    {
        $distribusi = $pesanan->distribusi;

        if (! $distribusi) {
            return back()->with('error', 'Distribusi tidak ditemukan');
        }

        if ($distribusi->status_pengiriman === 'selesai') {
            return back()->with('success', 'Distribusi sudah selesai');
        }

        if ($distribusi->status_pengiriman !== 'terkirim') {
            return back()->with('error', 'Distribusi belum dikonfirmasi kurir. Status harus terkirim sebelum diselesaikan admin.');
        }

        DB::transaction(function () use ($distribusi, $pesanan) {
            $distribusi->update([
                'status_pengiriman' => 'selesai',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
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

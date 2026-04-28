<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Piutang;
use Illuminate\Support\Facades\DB;

class DistribusiController extends Controller
{
    public function index()
    {
        $distribusis = Distribusi::with(['pesanan.toko', 'kurir', 'approver'])
            ->latest()
            ->get();

        return view('distribusi.index', compact('distribusis'));
    }

    public function create()
    {
        return view('distribusi.create', [
            'pesanans' => Pesanan::whereIn('status_pesanan', ['draft', 'diproses', 'dikirim'])
                ->doesntHave('distribusi')
                ->get(),
            'kurirs' => User::where('role', 'kurir')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'kurir_id' => 'required|exists:users,id',
        ], [
            'pesanan_id.required' => 'Pesanan wajib dipilih.',
            'pesanan_id.exists' => 'Pesanan yang dipilih tidak valid.',
            'kurir_id.required' => 'Kurir wajib dipilih.',
            'kurir_id.exists' => 'Kurir yang dipilih tidak valid.',
        ]);

        $pesanan = Pesanan::findOrFail($request->pesanan_id);

        Distribusi::create([
            'pesanan_id' => $request->pesanan_id,
            'kurir_id' => $request->kurir_id,
            'tanggal_kirim' => $pesanan->tanggal_kirim,
            'status_pengiriman' => 'pending',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('distribusis.index')->with('success', 'Distribusi berhasil dibuat');
    }

    public function edit(Distribusi $distribusi)
    {
        return view('distribusi.edit', [
            'distribusi' => $distribusi->load('pesanan.toko'),
            'kurirs' => User::where('role', 'kurir')->get(),
        ]);
    }

    public function update(Request $request, Distribusi $distribusi)
    {
        $request->validate([
            'kurir_id' => 'required|exists:users,id',
            'status_pengiriman' => 'required|in:pending,dikirim,terkirim,selesai',
            'catatan' => 'nullable|string',
        ], [
            'kurir_id.required' => 'Kurir wajib dipilih.',
            'kurir_id.exists' => 'Kurir yang dipilih tidak valid.',
            'status_pengiriman.required' => 'Status pengiriman wajib dipilih.',
            'status_pengiriman.in' => 'Status pengiriman yang dipilih tidak valid.',
        ]);

        $distribusi->update([
            'kurir_id' => $request->kurir_id,
            'tanggal_kirim' => $distribusi->pesanan->tanggal_kirim,
            'status_pengiriman' => $request->status_pengiriman,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('distribusis.index')->with('success', 'Distribusi berhasil diperbarui');
    }

    public function destroy(Distribusi $distribusi)
    {
        $distribusi->delete();

        return redirect()->route('distribusis.index')->with('success', 'Distribusi berhasil dihapus');
    }

    public function selesai(Distribusi $distribusi)
    {
        // cegah double proses
        if ($distribusi->status_pengiriman === 'selesai') {
            return back()->with('success', 'Distribusi sudah selesai');
        }

        if ($distribusi->status_pengiriman !== 'terkirim') {
            return back()->with('error', 'Distribusi belum dikonfirmasi kurir. Status harus terkirim sebelum diselesaikan admin.');
        }

        DB::transaction(function () use ($distribusi) {
            // update status distribusi
            $distribusi->update([
                'status_pengiriman' => 'selesai',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $pesanan = $distribusi->pesanan;

            // buat piutang hanya jika tempo
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

        return redirect()->route('distribusis.index')->with('success', 'Distribusi selesai & piutang diproses');
    }
}

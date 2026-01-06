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
        $distribusis = Distribusi::with(['pesanan.toko', 'kurir'])
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
            'kurir_id' => 'nullable|exists:users,id',
            'tanggal_kirim' => 'required|date',
        ]);

        Distribusi::create([
            'pesanan_id' => $request->pesanan_id,
            'kurir_id' => $request->kurir_id,
            'tanggal_kirim' => $request->tanggal_kirim,
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
            'kurir_id' => 'nullable|exists:users,id',
            'tanggal_kirim' => 'required|date',
            'status_pengiriman' => 'required',
        ]);

        $distribusi->update($request->only(['kurir_id', 'tanggal_kirim', 'status_pengiriman', 'catatan']));

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

        DB::transaction(function () use ($distribusi) {
            // update status distribusi
            $distribusi->update([
                'status_pengiriman' => 'selesai',
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

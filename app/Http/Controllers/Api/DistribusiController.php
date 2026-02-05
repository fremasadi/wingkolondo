<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use Illuminate\Http\Request;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $kurir = $request->user();

        // keamanan: pastikan kurir
        if ($kurir->role !== 'kurir') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $distribusis = Distribusi::with([
                'pesanan.toko',
                'pesanan.details.produk'
            ])
            ->where('kurir_id', $kurir->id)
            ->orderBy('tanggal_kirim', 'desc')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'tanggal_kirim' => $d->tanggal_kirim,
                    'status_pengiriman' => $d->status_pengiriman,
                    'catatan' => $d->catatan,
                    'toko' => [
                        'nama' => $d->pesanan->toko->nama_toko,
                        'alamat' => $d->pesanan->toko->alamat,
                        'no_hp' => $d->pesanan->toko->no_hp,
                    ],
                    'pesanan' => [
                        'id' => $d->pesanan->id,
                        'tanggal_pesanan' => $d->pesanan->tanggal_pesanan,
                        'total_harga' => $d->pesanan->total_harga,
                        'metode_pembayaran' => $d->pesanan->metode_pembayaran,
                    ],
                    'items' => $d->pesanan->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'produk_id' => $detail->produk_id,
                            'nama_produk' => $detail->produk->nama_produk,
                            'qty' => $detail->qty,
                            'harga' => $detail->harga,
                            'subtotal' => $detail->subtotal,
                        ];
                    }),
                ];
            });

        return response()->json([
            'message' => 'Daftar distribusi',
            'data' => $distribusis
        ]);
    }
}
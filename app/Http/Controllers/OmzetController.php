<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Piutang;
use Illuminate\Http\Request;

class OmzetController extends Controller
{
    public function index(Request $request)
    {
        $mulai = $request->mulai;
        $sampai = $request->sampai;

        // Pesanan langsung bayar dihitung net setelah retur selesai.
        $cashOrders = Pesanan::whereIn('metode_pembayaran', ['cash','transfer'])
            ->when($mulai, fn($q) => $q->whereDate('tanggal_pesanan', '>=', $mulai))
            ->when($sampai, fn($q) => $q->whereDate('tanggal_pesanan', '<=', $sampai))
            ->withSum([
                'returs as retur_selesai_total' => fn ($query) => $query->where('status', 'selesai'),
            ], 'total_refund')
            ->get();

        $cash = $cashOrders->sum(function (Pesanan $pesanan) {
            $netto = (float) $pesanan->total_harga - (float) ($pesanan->retur_selesai_total ?? 0);

            return max(0, $netto);
        });

        // Piutang lunas otomatis ikut net karena total_tagihan dikurangi saat retur selesai.
        $tempo = Piutang::where('status', 'lunas')
            ->when($mulai, fn($q) => $q->whereDate('updated_at', '>=', $mulai))
            ->when($sampai, fn($q) => $q->whereDate('updated_at', '<=', $sampai))
            ->sum('total_tagihan');

        $totalOmzet = $cash + $tempo;

        return view('omzet.index', compact(
            'cash',
            'tempo',
            'totalOmzet',
            'mulai',
            'sampai'
        ));
    }
}

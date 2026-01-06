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

        // Pesanan langsung bayar
        $cash = Pesanan::whereIn('metode_pembayaran', ['cash','transfer'])
            ->when($mulai, fn($q) => $q->whereDate('tanggal_pesanan', '>=', $mulai))
            ->when($sampai, fn($q) => $q->whereDate('tanggal_pesanan', '<=', $sampai))
            ->sum('total_harga');

        // Piutang lunas
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
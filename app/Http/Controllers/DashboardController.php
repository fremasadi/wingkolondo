<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Distribusi;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\Piutang;
use App\Models\Retur;
use App\Models\BahanBaku;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Umum
        $totalPesanan = Pesanan::count();
        $totalToko = Toko::count();
        $totalProduk = Produk::count();

        // Pesanan Hari Ini
        $pesananHariIni = Pesanan::whereDate('tanggal_pesanan', Carbon::today())->count();

        // Total Pendapatan Bulan Ini
        $pendapatanBulanIni = Pesanan::whereMonth('tanggal_pesanan', Carbon::now()->month)
            ->whereYear('tanggal_pesanan', Carbon::now()->year)
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->sum('total_harga');

        // Total Piutang
        $totalPiutang = Piutang::where('status', '!=', 'lunas')->sum('sisa_tagihan');

        // Piutang Jatuh Tempo
        $piutangJatuhTempo = Piutang::where('status', '!=', 'lunas')
            ->where('jatuh_tempo', '<=', Carbon::now()->addDays(7))
            ->count();

        // Status Pesanan
        $pesananDraft = Pesanan::where('status_pesanan', 'draft')->count();
        $pesananDikonfirmasi = Pesanan::where('status_pesanan', 'dikonfirmasi')->count();
        $pesananDiproses = Pesanan::where('status_pesanan', 'diproses')->count();
        $pesananSelesai = Pesanan::where('status_pesanan', 'selesai')->count();

        // Status Distribusi
        $distribusiMenunggu = Distribusi::where('status_pengiriman', 'menunggu')->count();
        $distribusiDikirim = Distribusi::where('status_pengiriman', 'dikirim')->count();
        $distribusiSelesai = Distribusi::where('status_pengiriman', 'selesai')->count();

        // Produk Stok Rendah (< 50)
        $produkStokRendah = Produk::where('stok', '<', 50)->count();

        // Bahan Baku Stok Rendah (< 100)
        $bahanBakuStokRendah = BahanBaku::where('stok', '<', 100)->count();

        // Total Retur Bulan Ini
        $returBulanIni = Retur::whereMonth('tanggal_retur', Carbon::now()->month)
            ->whereYear('tanggal_retur', Carbon::now()->year)
            ->sum('total_retur');

        // Grafik Pendapatan 7 Hari Terakhir
        $pendapatan7Hari = Pesanan::where('tanggal_pesanan', '>=', Carbon::now()->subDays(6))
            ->where('status_pesanan', '!=', 'dibatalkan')
            ->select(
                DB::raw('DATE(tanggal_pesanan) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Produk Terlaris Bulan Ini
        $produkTerlaris = DB::table('detail_pesanans')
            ->join('produks', 'detail_pesanans.produk_id', '=', 'produks.id')
            ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
            ->whereMonth('pesanans.tanggal_pesanan', Carbon::now()->month)
            ->whereYear('pesanans.tanggal_pesanan', Carbon::now()->year)
            ->select('produks.nama_produk', DB::raw('SUM(detail_pesanans.qty) as total_qty'))
            ->groupBy('produks.id', 'produks.nama_produk')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Pesanan Terbaru
        $pesananTerbaru = Pesanan::with('toko')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPesanan',
            'totalToko',
            'totalProduk',
            'pesananHariIni',
            'pendapatanBulanIni',
            'totalPiutang',
            'piutangJatuhTempo',
            'pesananDraft',
            'pesananDikonfirmasi',
            'pesananDiproses',
            'pesananSelesai',
            'distribusiMenunggu',
            'distribusiDikirim',
            'distribusiSelesai',
            'produkStokRendah',
            'bahanBakuStokRendah',
            'returBulanIni',
            'pendapatan7Hari',
            'produkTerlaris',
            'pesananTerbaru'
        ));
    }
}
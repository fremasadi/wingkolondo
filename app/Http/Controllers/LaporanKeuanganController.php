<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Piutang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request): View
    {
        return view('laporan_keuangan.index', $this->buildReportData($request));
    }

    public function print(Request $request): View
    {
        return view('laporan_keuangan.print', $this->buildReportData($request));
    }

    private function buildReportData(Request $request): array
    {
        $now = now();
        $selectedMonth = max(1, min(12, (int) $request->query('bulan', $now->month)));
        $selectedYear = max(2000, min(2100, (int) $request->query('tahun', $now->year)));
        $selectedSumber = $request->query('sumber');

        if (! in_array($selectedSumber, ['cash_transfer', 'tempo', null, ''], true)) {
            $selectedSumber = null;
        }

        $periodStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $cashEntries = $this->buildCashTransferEntries($periodStart, $periodEnd);
        $tempoEntries = $this->buildTempoEntries($periodStart, $periodEnd);

        $entries = $cashEntries
            ->concat($tempoEntries)
            ->when($selectedSumber, fn (Collection $collection) => $collection->where('sumber_key', $selectedSumber))
            ->sortBy(fn (object $entry) => $entry->tanggal->format('Ymd') . '-' . $entry->key)
            ->values();

        $cashBruto = (float) $cashEntries->sum('bruto');
        $cashRetur = (float) $cashEntries->sum('pengurang');
        $cashNet = (float) $cashEntries->sum('netto');
        $tempoNet = (float) $tempoEntries->sum('netto');
        $totalOmzet = $cashNet + $tempoNet;

        return [
            'entries' => $entries,
            'cashBruto' => $cashBruto,
            'cashRetur' => $cashRetur,
            'cashNet' => $cashNet,
            'tempoNet' => $tempoNet,
            'totalOmzet' => $totalOmzet,
            'totalEntries' => $entries->count(),
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedSumber' => $selectedSumber ?: null,
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
            'reportTitle' => 'Laporan Keuangan Detail Bulan ' . strtoupper($this->monthOptions()[$selectedMonth]) . ' ' . $selectedYear,
        ];
    }

    private function buildCashTransferEntries(Carbon $periodStart, Carbon $periodEnd): Collection
    {
        return Pesanan::query()
            ->with(['toko'])
            ->whereIn('metode_pembayaran', ['cash', 'transfer'])
            ->when($periodStart, fn ($query) => $query->whereDate('tanggal_pesanan', '>=', $periodStart->toDateString()))
            ->when($periodEnd, fn ($query) => $query->whereDate('tanggal_pesanan', '<=', $periodEnd->toDateString()))
            ->withSum([
                'returs as retur_selesai_total' => fn ($query) => $query->where('status', 'selesai'),
            ], 'total_refund')
            ->orderBy('tanggal_pesanan')
            ->get()
            ->map(function (Pesanan $pesanan) {
                $bruto = (float) $pesanan->total_harga;
                $pengurang = min($bruto, (float) ($pesanan->retur_selesai_total ?? 0));
                $netto = max(0, $bruto - $pengurang);

                return $this->makeEntry(
                    key: 'cash-' . $pesanan->id,
                    tanggal: Carbon::parse($pesanan->tanggal_pesanan),
                    referensi: $pesanan->order_code ?? ('#' . $pesanan->id),
                    pihak: $pesanan->toko->nama_toko ?? 'Toko',
                    jenisTransaksi: 'Penjualan Cash/Transfer',
                    metodePembayaran: strtoupper($pesanan->metode_pembayaran),
                    bruto: $bruto,
                    pengurang: $pengurang,
                    netto: $netto,
                    sumber: 'Cash/Transfer',
                    sumberKey: 'cash_transfer'
                );
            });
    }

    private function buildTempoEntries(Carbon $periodStart, Carbon $periodEnd): Collection
    {
        return Piutang::query()
            ->with(['toko', 'pesanan'])
            ->where('status', 'lunas')
            ->whereDate('updated_at', '>=', $periodStart->toDateString())
            ->whereDate('updated_at', '<=', $periodEnd->toDateString())
            ->orderBy('updated_at')
            ->get()
            ->map(function (Piutang $piutang) {
                $netto = (float) $piutang->total_tagihan;

                return $this->makeEntry(
                    key: 'tempo-' . $piutang->id,
                    tanggal: Carbon::parse($piutang->updated_at),
                    referensi: $piutang->pesanan->order_code ?? ('#' . $piutang->pesanan_id),
                    pihak: $piutang->toko->nama_toko ?? 'Toko',
                    jenisTransaksi: 'Pelunasan Piutang Tempo',
                    metodePembayaran: 'TEMPO',
                    bruto: $netto,
                    pengurang: 0,
                    netto: $netto,
                    sumber: 'Tempo Lunas',
                    sumberKey: 'tempo'
                );
            });
    }

    private function makeEntry(
        string $key,
        Carbon $tanggal,
        string $referensi,
        string $pihak,
        string $jenisTransaksi,
        string $metodePembayaran,
        float $bruto,
        float $pengurang,
        float $netto,
        string $sumber,
        string $sumberKey
    ): object {
        return (object) [
            'key' => $key,
            'tanggal' => $tanggal,
            'referensi' => $referensi,
            'pihak' => $pihak,
            'jenis_transaksi' => $jenisTransaksi,
            'metode_pembayaran' => $metodePembayaran,
            'bruto' => $bruto,
            'pengurang' => $pengurang,
            'netto' => $netto,
            'sumber' => $sumber,
            'sumber_key' => $sumberKey,
        ];
    }

    private function monthOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function yearOptions(): array
    {
        $currentYear = (int) now()->year;

        return range($currentYear - 4, $currentYear + 1);
    }
}

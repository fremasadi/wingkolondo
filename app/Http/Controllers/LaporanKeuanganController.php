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
        $tipeFilter = $request->query('tipe_filter', 'bulan');
        if (! in_array($tipeFilter, ['bulan', 'tahun', 'minggu', 'range'], true)) {
            $tipeFilter = 'bulan';
        }

        $selectedMonth = null;
        $selectedYear = null;
        $selectedDateMinggu = null;
        $tanggalMulai = null;
        $tanggalSelesai = null;

        switch ($tipeFilter) {
            case 'tahun':
                $selectedYear = max(2000, min(2100, (int) $request->query('tahun', $now->year)));
                $periodStart = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $periodEnd = $periodStart->copy()->endOfYear();
                $reportTitle = 'Laporan Keuangan Detail Tahun ' . $selectedYear;
                break;

            case 'minggu':
                $selectedDateMinggu = $request->query('tanggal_minggu', $now->toDateString());
                try {
                    $pivotDate = Carbon::parse($selectedDateMinggu);
                } catch (\Exception $e) {
                    $pivotDate = $now->copy();
                    $selectedDateMinggu = $now->toDateString();
                }
                $periodStart = $pivotDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $periodEnd = $pivotDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $reportTitle = 'Laporan Keuangan Detail Mingguan (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
                break;

            case 'range':
                $defaultStart = $now->copy()->startOfMonth()->toDateString();
                $defaultEnd = $now->copy()->endOfMonth()->toDateString();
                $tanggalMulai = $request->query('tanggal_mulai', $defaultStart);
                $tanggalSelesai = $request->query('tanggal_selesai', $defaultEnd);

                try {
                    $periodStart = Carbon::parse($tanggalMulai)->startOfDay();
                } catch (\Exception $e) {
                    $periodStart = Carbon::parse($defaultStart)->startOfDay();
                    $tanggalMulai = $defaultStart;
                }
                try {
                    $periodEnd = Carbon::parse($tanggalSelesai)->endOfDay();
                } catch (\Exception $e) {
                    $periodEnd = Carbon::parse($defaultEnd)->endOfDay();
                    $tanggalSelesai = $defaultEnd;
                }
                $reportTitle = 'Laporan Keuangan Detail Periode (' . $periodStart->translatedFormat('d M Y') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
                break;

            case 'bulan':
            default:
                $selectedMonth = max(1, min(12, (int) $request->query('bulan', $now->month)));
                $selectedYear = max(2000, min(2100, (int) $request->query('tahun', $now->year)));
                $periodStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
                $periodEnd = $periodStart->copy()->endOfMonth();
                $reportTitle = 'Laporan Keuangan Detail Bulan ' . strtoupper($this->monthOptions()[$selectedMonth]) . ' ' . $selectedYear;
                break;
        }

        $selectedSumber = $request->query('sumber');
        if (! in_array($selectedSumber, ['cash_transfer', 'tempo', null, ''], true)) {
            $selectedSumber = null;
        }

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

        // Group entries for chart
        $chartData = [];
        switch ($tipeFilter) {
            case 'tahun':
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = $this->monthOptions()[$m];
                    $chartData[$monthName] = 0;
                }
                foreach ($entries as $entry) {
                    $mNum = (int)$entry->tanggal->format('n');
                    if (isset($this->monthOptions()[$mNum])) {
                        $monthName = $this->monthOptions()[$mNum];
                        $chartData[$monthName] += $entry->netto;
                    }
                }
                break;

            case 'minggu':
                $daysOfWeek = [
                    1 => 'Senin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu',
                    7 => 'Minggu',
                ];
                foreach ($daysOfWeek as $dayName) {
                    $chartData[$dayName] = 0;
                }
                foreach ($entries as $entry) {
                    $dayNum = (int)$entry->tanggal->format('N');
                    if (isset($daysOfWeek[$dayNum])) {
                        $chartData[$daysOfWeek[$dayNum]] += $entry->netto;
                    }
                }
                break;

            case 'bulan':
            case 'range':
            default:
                $diffInDays = $periodStart->diffInDays($periodEnd);
                if ($diffInDays > 62) {
                    $current = $periodStart->copy()->startOfMonth();
                    while ($current->lte($periodEnd)) {
                        $monthYearKey = $current->translatedFormat('M Y');
                        $chartData[$monthYearKey] = 0;
                        $current->addMonth();
                    }
                    foreach ($entries as $entry) {
                        $key = $entry->tanggal->translatedFormat('M Y');
                        if (isset($chartData[$key])) {
                            $chartData[$key] += $entry->netto;
                        }
                    }
                } else {
                    $current = $periodStart->copy();
                    while ($current->lte($periodEnd)) {
                        $key = $current->translatedFormat('d M');
                        $chartData[$key] = 0;
                        $current->addDay();
                    }
                    foreach ($entries as $entry) {
                        $key = $entry->tanggal->translatedFormat('d M');
                        if (isset($chartData[$key])) {
                            $chartData[$key] += $entry->netto;
                        }
                    }
                }
                break;
        }

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
            'tipeFilter' => $tipeFilter,
            'selectedMonth' => $selectedMonth ?: ($now->month),
            'selectedYear' => $selectedYear ?: ($now->year),
            'selectedDateMinggu' => $selectedDateMinggu ?: ($now->toDateString()),
            'tanggalMulai' => $tanggalMulai ?: ($periodStart->toDateString()),
            'tanggalSelesai' => $tanggalSelesai ?: ($periodEnd->toDateString()),
            'selectedSumber' => $selectedSumber ?: null,
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
            'reportTitle' => $reportTitle,
            'chartLabels' => array_keys($chartData),
            'chartValues' => array_values($chartData),
            'printQuery' => $request->query(),
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

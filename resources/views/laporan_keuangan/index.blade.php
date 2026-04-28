@extends('layouts.app')

@php
    $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $printQuery = [
        'bulan' => $selectedMonth,
        'tahun' => $selectedYear,
    ];

    if ($selectedSumber) {
        $printQuery['sumber'] = $selectedSumber;
    }
@endphp

@section('content')
<div class="container-xxl container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Laporan Keuangan Detail</h4>
            <p class="text-muted mb-0">Laporan ini mengambil data otomatis dari pesanan cash/transfer dan piutang lunas.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('laporan-keuangan.print', $printQuery) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bx bx-printer"></i> Print
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @foreach($monthOptions as $monthValue => $monthLabel)
                            <option value="{{ $monthValue }}" @selected($selectedMonth === $monthValue)>{{ $monthLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sumber</label>
                    <select name="sumber" class="form-select">
                        <option value="">Semua Sumber</option>
                        <option value="cash_transfer" @selected($selectedSumber === 'cash_transfer')>Cash / Transfer</option>
                        <option value="tempo" @selected($selectedSumber === 'tempo')>Tempo Lunas</option>
                    </select>
                </div>

                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-filter-alt"></i> Filter
                    </button>
                    <a href="{{ route('laporan-keuangan.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 border-primary">
                <div class="card-body">
                    <div class="text-muted small">Penjualan Cash/Transfer Bruto</div>
                    <h5 class="mb-0">{{ $formatCurrency($cashBruto) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-success">
                <div class="card-body">
                    <div class="text-muted small">Pengurang Retur Cash/Transfer</div>
                    <h5 class="mb-0 text-danger">{{ $formatCurrency($cashRetur) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-danger">
                <div class="card-body">
                    <div class="text-muted small">Cash/Transfer Netto</div>
                    <h5 class="mb-0 text-success">{{ $formatCurrency($cashNet) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-4 border-warning">
                <div class="card-body">
                    <div class="text-muted small">Tempo Lunas</div>
                    <h5 class="mb-0 text-info">{{ $formatCurrency($tempoNet) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-start border-4 border-dark">
                <div class="card-body">
                    <div class="text-muted small">Total Omzet</div>
                    <h4 class="mb-0">{{ $formatCurrency($totalOmzet) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-start border-4 border-secondary">
                <div class="card-body">
                    <div class="text-muted small">Jumlah Transaksi</div>
                    <h4 class="mb-0">{{ number_format($totalEntries) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr class="table-danger text-center align-middle">
                            <th style="min-width: 120px;">Tanggal/Bulan</th>
                            <th style="min-width: 150px;">Referensi</th>
                            <th style="min-width: 220px;">Toko</th>
                            <th style="min-width: 180px;">Jenis Transaksi</th>
                            <th style="min-width: 120px;">Sumber</th>
                            <th style="min-width: 120px;">Metode</th>
                            <th style="min-width: 150px;">Nilai Bruto</th>
                            <th style="min-width: 150px;">Pengurang Retur</th>
                            <th style="min-width: 170px;">Nilai Netto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td>{{ $entry->tanggal->translatedFormat('d F Y') }}</td>
                                <td>{{ $entry->referensi }}</td>
                                <td>{{ $entry->pihak }}</td>
                                <td>{{ $entry->jenis_transaksi }}</td>
                                <td class="text-center">{{ $entry->sumber }}</td>
                                <td class="text-center">{{ $entry->metode_pembayaran }}</td>
                                <td class="text-end">{{ $formatCurrency($entry->bruto) }}</td>
                                <td class="text-end">{{ $entry->pengurang > 0 ? $formatCurrency($entry->pengurang) : '-' }}</td>
                                <td class="text-end fw-semibold">{{ $formatCurrency($entry->netto) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada data laporan keuangan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="6" class="text-end">Total Periode</th>
                            <th class="text-end">{{ $formatCurrency($cashBruto + $tempoNet) }}</th>
                            <th class="text-end text-danger">{{ $formatCurrency($cashRetur) }}</th>
                            <th class="text-end fw-bold">{{ $formatCurrency($totalOmzet) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

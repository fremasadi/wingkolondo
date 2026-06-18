@extends('layouts.app')

@php
    $formatCurrency = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
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
                <div class="col-md-3">
                    <label class="form-label">Tipe Filter</label>
                    <select name="tipe_filter" id="tipeFilterSelect" class="form-select">
                        <option value="bulan" @selected($tipeFilter === 'bulan')>Bulanan</option>
                        <option value="tahun" @selected($tipeFilter === 'tahun')>Tahunan</option>
                        <option value="minggu" @selected($tipeFilter === 'minggu')>Mingguan</option>
                        <option value="range" @selected($tipeFilter === 'range')>Range Tanggal</option>
                    </select>
                </div>

                <div class="col-md-3 filter-field filter-bulan">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @foreach($monthOptions as $monthValue => $monthLabel)
                            <option value="{{ $monthValue }}" @selected($selectedMonth == $monthValue)>{{ $monthLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 filter-field filter-bulan filter-tahun">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}" @selected($selectedYear == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 filter-field filter-minggu">
                    <label class="form-label">Tanggal Acuan Minggu</label>
                    <input type="date" name="tanggal_minggu" class="form-control" value="{{ $selectedDateMinggu }}">
                </div>

                <div class="col-md-3 filter-field filter-range">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}">
                </div>
                <div class="col-md-3 filter-field filter-range">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai }}">
                </div>

                <div class="col-md-3">
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
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Penjualan Cash/Transfer Bruto</div>
                    <h5 class="mb-0">{{ $formatCurrency($cashBruto) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Pengurang Retur Cash/Transfer</div>
                    <h5 class="mb-0 ">{{ $formatCurrency($cashRetur) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Cash/Transfer Netto</div>
                    <h5 class="mb-0 ">{{ $formatCurrency($cashNet) }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Tempo Lunas</div>
                    <h5 class="mb-0 ">{{ $formatCurrency($tempoNet) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Total Omzet</div>
                    <h4 class="mb-0">{{ $formatCurrency($totalOmzet) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Jumlah Transaksi</div>
                    <h4 class="mb-0">{{ number_format($totalEntries) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Grafik Perkembangan Omzet (Nilai Netto)</h5>
            <small class="text-muted">Berdasarkan filter aktif</small>
        </div>
        <div class="card-body">
            <div id="chartOmzet"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr class="text-center align-middle">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeFilterSelect = document.getElementById('tipeFilterSelect');
    function toggleFields() {
        if (!tipeFilterSelect) return;
        const val = tipeFilterSelect.value;
        
        document.querySelectorAll('.filter-field').forEach(el => {
            el.closest('.col-md-3, .col-md-6').style.setProperty('display', 'none', 'important');
        });
        
        if (val === 'bulan') {
            document.querySelectorAll('.filter-bulan').forEach(el => {
                el.closest('.col-md-3, .col-md-6').style.setProperty('display', 'block', 'important');
            });
        } else if (val === 'tahun') {
            document.querySelectorAll('.filter-tahun').forEach(el => {
                el.closest('.col-md-3, .col-md-6').style.setProperty('display', 'block', 'important');
            });
        } else if (val === 'minggu') {
            document.querySelectorAll('.filter-minggu').forEach(el => {
                el.closest('.col-md-3, .col-md-6').style.setProperty('display', 'block', 'important');
            });
        } else if (val === 'range') {
            document.querySelectorAll('.filter-range').forEach(el => {
                el.closest('.col-md-3, .col-md-6').style.setProperty('display', 'block', 'important');
            });
        }
    }
    
    if (tipeFilterSelect) {
        tipeFilterSelect.addEventListener('change', toggleFields);
        toggleFields();
    }

    const labels = @json($chartLabels);
    const dataValues = @json($chartValues);

    const options = {
        series: [{
            name: 'Netto Omzet',
            data: dataValues
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: labels,
            labels: {
                style: {
                    colors: '#a1acb8',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                },
                style: {
                    colors: '#a1acb8'
                }
            }
        },
        colors: ['#696cff'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chartOmzet"), options);
    chart.render();
});
</script>
@endpush

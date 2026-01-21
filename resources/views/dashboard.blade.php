@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row mb-4">
        <div class="col">
            <h4 class="fw-bold mb-0">Dashboard</h4>
            <p class="text-muted mb-0">Selamat datang di sistem manajemen distribusi</p>
        </div>
        <div class="col-auto">
            <span class="badge bg-label-primary">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted">Total Pesanan</span>
                            <div class="d-flex align-items-center my-1">
                                <h3 class="mb-0 me-2">{{ number_format($totalPesanan) }}</h3>
                                <span class="badge bg-label-success">
                                    <i class="bx bx-up-arrow-alt"></i>
                                </span>
                            </div>
                            <small class="text-muted">Pesanan Hari Ini: <strong>{{ $pesananHariIni }}</strong></small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-cart bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted">Pendapatan Bulan Ini</span>
                            <div class="d-flex align-items-center my-1">
                                <h3 class="mb-0 me-2">{{ number_format($pendapatanBulanIni / 1000000, 1) }}M</h3>
                            </div>
                            <small class="text-muted">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-wallet bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted">Total Piutang</span>
                            <div class="d-flex align-items-center my-1">
                                <h3 class="mb-0 me-2">{{ number_format($totalPiutang / 1000000, 1) }}M</h3>
                                @if($piutangJatuhTempo > 0)
                                <span class="badge bg-label-warning">{{ $piutangJatuhTempo }} Jatuh Tempo</span>
                                @endif
                            </div>
                            <small class="text-muted">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-money bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted">Total Toko</span>
                            <div class="d-flex align-items-center my-1">
                                <h3 class="mb-0 me-2">{{ $totalToko }}</h3>
                            </div>
                            <small class="text-muted">Produk: <strong>{{ $totalProduk }}</strong></small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-store bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Status Pesanan -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Status Pesanan</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="statusPesanan" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="statusPesanan">
                            <a class="dropdown-item" href="{{ route('pesanans.index') }}">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-file"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Draft</h6>
                                    <small class="text-muted">Belum dikonfirmasi</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $pesananDraft }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-check-circle"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Dikonfirmasi</h6>
                                    <small class="text-muted">Menunggu proses</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $pesananDikonfirmasi }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-time"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Diproses</h6>
                                    <small class="text-muted">Sedang produksi</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $pesananDiproses }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-0">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-double"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Selesai</h6>
                                    <small class="text-muted">Berhasil dikirim</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $pesananSelesai }}</h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Status Distribusi -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Status Distribusi</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="statusDistribusi" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="statusDistribusi">
                            <a class="dropdown-item" href="#">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column gap-1">
                            <h2 class="mb-2">{{ $distribusiMenunggu + $distribusiDikirim + $distribusiSelesai }}</h2>
                            <span>Total Distribusi</span>
                        </div>
                        <div id="distribusiChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-3 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded-pill bg-label-secondary me-3 p-2">
                                            <i class="bx bx-pause bx-xs"></i>
                                        </div>
                                        <h6 class="mb-0">Menunggu</h6>
                                    </div>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">{{ $distribusiMenunggu }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-3 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded-pill bg-label-primary me-3 p-2">
                                            <i class="bx bx-car bx-xs"></i>
                                        </div>
                                        <h6 class="mb-0">Dikirim</h6>
                                    </div>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">{{ $distribusiDikirim }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <div class="d-flex align-items-center">
                                        <div class="badge rounded-pill bg-label-success me-3 p-2">
                                            <i class="bx bx-check bx-xs"></i>
                                        </div>
                                        <h6 class="mb-0">Selesai</h6>
                                    </div>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">{{ $distribusiSelesai }}</h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Peringatan Stok -->
        <div class="col-md-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0">Peringatan Stok</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @if($produkStokRendah > 0)
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0 text-danger">Produk Stok Rendah</h6>
                                    <small class="text-muted">Segera lakukan produksi</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0 text-danger">{{ $produkStokRendah }}</h6>
                                </div>
                            </div>
                        </li>
                        @else
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0 text-success">Stok Produk Aman</h6>
                                    <small class="text-muted">Semua stok mencukupi</small>
                                </div>
                            </div>
                        </li>
                        @endif

                        @if($bahanBakuStokRendah > 0)
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-error-alt"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0 text-warning">Bahan Baku Rendah</h6>
                                    <small class="text-muted">Segera lakukan pembelian</small>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0 text-warning">{{ $bahanBakuStokRendah }}</h6>
                                </div>
                            </div>
                        </li>
                        @else
                        <li class="d-flex mb-3 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0 text-success">Bahan Baku Aman</h6>
                                    <small class="text-muted">Semua stok mencukupi</small>
                                </div>
                            </div>
                        </li>
                        @endif

                        <li class="d-flex">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-undo"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Total Retur Bulan Ini</h6>
                                    <small class="text-muted">Rp {{ number_format($returBulanIni, 0, ',', '.') }}</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Grafik Pendapatan -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Pendapatan 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="pendapatanChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Produk Terlaris -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0">Produk Terlaris Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @forelse($produkTerlaris as $index => $produk)
                        <li class="d-flex mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">{{ $index + 1 }}</span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $produk->nama_produk }}</h6>
                                    <small class="text-muted">{{ $produk->total_qty }} pcs terjual</small>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted py-3">
                            <i class="bx bx-package bx-md"></i>
                            <p class="mb-0 mt-2">Belum ada data penjualan</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Pesanan Terbaru -->
    {{-- <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Pesanan Terbaru</h5>
                    <a href="{{ route('pesanans.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Toko</th>
                                <th>Tanggal Pesanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($pesananTerbaru as $pesanan)
                            <tr>
                                <td><strong>#{{ $pesanan->id }}</strong></td>
                                <td>{{ $pesanan->toko->nama_toko }}</td>
                                <td>{{ \Carbon\Carbon::parse($pesanan->tanggal_pesanan)->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'draft' => 'bg-label-secondary',
                                            'dikonfirmasi' => 'bg-label-info',
                                            'diproses' => 'bg-label-warning',
                                            'selesai' => 'bg-label-success',
                                            'dibatalkan' => 'bg-label-danger'
                                        ];
                                    @endphp
                                    <span class="badge {{ $badgeClass[$pesanan->status_pesanan] ?? 'bg-label-secondary' }}">
                                        {{ ucfirst($pesanan->status_pesanan) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('pesanans.show', $pesanan->id) }}" class="btn btn-sm btn-icon btn-label-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bx bx-cart bx-md"></i>
                                    <p class="mb-0 mt-2">Belum ada pesanan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Pendapatan 7 Hari Terakhir
const pendapatanData = @json($pendapatan7Hari);
const labels = pendapatanData.map(item => {
    const date = new Date(item.tanggal);
    return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
});
const data = pendapatanData.map(item => item.total);

const ctx = document.getElementById('pendapatanChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: data,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(context.parsed.y);
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection
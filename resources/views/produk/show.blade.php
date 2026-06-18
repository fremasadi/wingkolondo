@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Detail Produk</h4>
        <a href="{{ route('produks.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Info Produk -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Nama Produk</th>
                            <td>: {{ $produk->nama_produk }}</td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td>: <span class="badge bg-label-primary">{{ $produk->stok }}</span></td>
                        </tr>
                        <tr>
                            <th>Harga</th>
                            <td>: Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Kadaluarsa</th>
                            <td>:
                                @php $exp = $produk->tanggal_kadaluarsa; @endphp
                                @if(!$exp)
                                    <span class="text-muted">Belum pernah diproduksi</span>
                                @elseif($exp->isPast())
                                    <span class="text-danger fw-bold">
                                        <i class="bx bx-error-circle"></i>
                                        {{ $exp->format('d/m/Y') }}
                                        <small class="ms-1">(Sudah kadaluarsa {{ $exp->diffForHumans() }})</small>
                                    </span>
                                @elseif($exp->diffInDays(now()) <= 7)
                                    <span class="text-warning fw-bold">
                                        <i class="bx bx-time-five"></i>
                                        {{ $exp->format('d/m/Y') }}
                                        <small class="ms-1">({{ $exp->diffForHumans() }})</small>
                                    </span>
                                @else
                                    <span class="text-success fw-bold">
                                        <i class="bx bx-check-circle"></i>
                                        {{ $exp->format('d/m/Y') }}
                                        <small class="ms-1">({{ $exp->diffForHumans() }})</small>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Komposisi Bahan Baku -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>Komposisi Bahan Baku</h5>
                </div>
                <div class="card-body">
                    @if($produk->bahanBakus->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produk->bahanBakus as $bahan)
                                <tr>
                                    <td>{{ $bahan->nama_bahan }}</td>
                                    <td>{{ $bahan->pivot->qty }}</td>
                                    <td>{{ $bahan->satuan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada komposisi bahan baku untuk produk ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Produksi -->
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-history me-2"></i>Riwayat Produksi & Kadaluarsa</h5>
                </div>
                <div class="card-body p-0">
                    @if($riwayatProduksi->isEmpty())
                        <div class="p-3">
                            <div class="alert alert-info mb-0">
                                <i class="bx bx-info-circle me-2"></i>Belum ada riwayat produksi selesai untuk produk ini.
                            </div>
                        </div>
                    @else
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID Produksi</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Qty Diproduksi</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatProduksi as $i => $riwayat)
                                @php
                                    $expDate = \Carbon\Carbon::parse($riwayat->tanggal_produksi)->addDays(30);
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <a href="{{ route('produksis.show', $riwayat->produksi_id) }}" class="text-primary">
                                            #{{ $riwayat->produksi_id }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($riwayat->tanggal_produksi)->format('d/m/Y') }}</td>
                                    <td>{{ $riwayat->qty }} pcs</td>
                                    <td class="fw-bold">{{ $expDate->format('d/m/Y') }}</td>
                                    <td>
                                        @if($expDate->isPast())
                                            <span class="text-danger fw-bold"><i class="bx bx-error-circle"></i> Kadaluarsa</span>
                                        @elseif($expDate->diffInDays(now()) <= 7)
                                            <span class="text-warning fw-bold"><i class="bx bx-time-five"></i> Hampir Kadaluarsa</span>
                                        @else
                                            <span class="text-success fw-bold"><i class="bx bx-check-circle"></i> Masih Aman</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="d-flex gap-2">
        <a href="{{ route('produks.edit', $produk) }}" class="btn btn-warning">
            <i class="bx bx-edit"></i> Edit Produk
        </a>
    </div>

</div>
@endsection
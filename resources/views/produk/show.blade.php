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

    <!-- Tombol Aksi -->
    <div class="d-flex gap-2">
        <a href="{{ route('produks.edit', $produk) }}" class="btn btn-warning">
            <i class="bx bx-edit"></i> Edit Produk
        </a>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Produk</h4>
        <a href="{{ route('produks.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Stok</th>
                        <th>Harga (per pcs)</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $produk)
                    <tr>
                        <td>
                            {{ $produk->nama_produk }}
                            @if($produk->detail_produksis_count > 0)
                                <span class="badge bg-label-info ms-1" title="Digunakan dalam {{ $produk->detail_produksis_count }} produksi">
                                    <i class="bx bx-box"></i> {{ $produk->detail_produksis_count }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ $produk->stok }}
                            </span>
                        </td>
                        <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('produks.show', $produk) }}" class="btn btn-sm btn-info">
                                <i class="bx bx-show"></i>
                            </a>
                            <a href="{{ route('produks.edit', $produk) }}" class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>

                            @if($produk->detail_produksis_count == 0 && $produk->detail_returs_count == 0)
                                <form action="{{ route('produks.destroy', $produk) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus produk ini?')" class="btn btn-sm btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat dihapus karena sudah digunakan">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Data produk belum tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
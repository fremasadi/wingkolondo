@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Bahan Baku</h4>
        <a href="{{ route('bahan-bakus.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Bahan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Peringatan Stok Rendah --}}
    @if($stokRendah->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error bx-tada me-2 fs-4"></i>
                <div>
                    <strong>Peringatan Stok Rendah!</strong>
                    <p class="mb-0 mt-1">
                        {{ $stokRendah->count() }} bahan baku memiliki stok rendah (≤ 10):
                        <strong>{{ $stokRendah->pluck('nama_bahan')->implode(', ') }}</strong>
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Peringatan Stok Habis --}}
    @php
        $stokHabis = $bahanBakus->filter(fn($item) => $item->stok <= 0);
    @endphp
    @if($stokHabis->count() > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-x-circle bx-flashing me-2 fs-4"></i>
                <div>
                    <strong>Stok Habis!</strong>
                    <p class="mb-0 mt-1">
                        {{ $stokHabis->count() }} bahan baku sudah habis:
                        <strong>{{ $stokHabis->pluck('nama_bahan')->implode(', ') }}</strong>
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Bahan</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahanBakus as $item)
                    <tr>
                        <td>
                            {{ $item->nama_bahan }}
                            @if($item->stok <= 0)
                                <span class="badge bg-danger ms-1">Habis</span>
                            @elseif($item->stok <= 10)
                                <span class="badge bg-warning ms-1">Rendah</span>
                            @endif
                        </td>
                        <td>
                            @if($item->stok <= 0)
                                <span class="badge bg-label-danger">{{ $item->stok }}</span>
                            @elseif($item->stok <= 10)
                                <span class="badge bg-label-warning">{{ $item->stok }}</span>
                            @else
                                <span class="badge bg-label-success">{{ $item->stok }}</span>
                            @endif
                        </td>
                        <td>{{ $item->satuan }}</td>
                        <td>
                            <a href="{{ route('bahan-bakus.edit', $item) }}" class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('bahan-bakus.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus bahan baku ini?')" class="btn btn-sm btn-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Data bahan baku belum tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
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
        <div class="alert alert-success">{{ session('success') }}</div>
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
                        <td>{{ $item->nama_bahan }}</td>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ $item->stok }}
                            </span>
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
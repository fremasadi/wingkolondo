@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Toko</h4>
        <a href="{{ route('tokos.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Toko
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
                        <th>Nama Toko</th>
                        <th>Alamat</th>
                        <th>No HP</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokos as $toko)
                    <tr>
                        <td>{{ $toko->nama_toko }}</td>
                        <td>{{ $toko->alamat }}</td>
                        <td>{{ $toko->no_hp }}</td>
                        <td>
                            <a href="{{ route('tokos.edit', $toko) }}" class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('tokos.destroy', $toko) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus toko ini?')" class="btn btn-sm btn-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Data toko belum tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
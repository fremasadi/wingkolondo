@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Pesanan</h4>
        <a href="{{ route('pesanans.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Pesanan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Toko</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $pesanan)
                    <tr>
                        <td>{{ $pesanan->tanggal_pesanan }}</td>
                        <td>{{ $pesanan->toko->nama_toko }}</td>
                        <td>
                            <span class="badge bg-label-info">
                                {{ ucfirst($pesanan->status_pesanan) }}
                            </span>
                        </td>
                        <td>{{ $pesanan->total_harga_formatted }}</td>
                        <td>
                            <a href="{{ route('pesanans.edit', $pesanan) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('pesanans.destroy', $pesanan) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus pesanan?')"
                                        class="btn btn-sm btn-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Data pesanan kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
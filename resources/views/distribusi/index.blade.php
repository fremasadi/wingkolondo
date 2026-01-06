@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Distribusi Produk</h4>
        <a href="{{ route('distribusis.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Buat Distribusi
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
                        <th>Kurir</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distribusis as $distribusi)
                    <tr>
                        <td>{{ $distribusi->tanggal_kirim }}</td>
                        <td>{{ $distribusi->pesanan->toko->nama_toko }}</td>
                        <td>{{ $distribusi->kurir->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-label-info">
                                {{ ucfirst($distribusi->status_pengiriman) }}
                            </span>
                        </td>
                        <td>
                            @if($distribusi->status_pengiriman !== 'selesai')

                                <form action="{{ route('distribusis.selesai', $distribusi) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-success"
                                            onclick="return confirm('Tandai distribusi selesai?')">
                                        <i class="bx bx-check"></i>
                                    </button>
                                </form>

                            @endif

                            <a href="{{ route('distribusis.edit', $distribusi) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>

                            <form action="{{ route('distribusis.destroy', $distribusi) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Hapus distribusi?')"
                                        class="btn btn-sm btn-danger">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Data distribusi kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
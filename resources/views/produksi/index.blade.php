@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Produksi</h4>
        <a href="{{ route('produksis.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Produksi
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
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Detail</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produksis as $p)
                        <tr>
                            <td>{{ $p->tanggal_produksi }}</td>
                            <td>
                                <span class="badge bg-label-{{ $p->status === 'selesai' ? 'success' : 'warning' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td>
                                @foreach($p->details as $d)
                                    {{ $d->produk->nama_produk }} ({{ $d->qty }})<br>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('produksis.show', $p->id) }}" class="btn btn-sm btn-info">
                                    <i class="bx bx-show"></i>
                                </a>

                                @if($p->status !== 'selesai')
                                    <form action="{{ route('produksis.selesai', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success" onclick="return confirm('Selesaikan produksi ini?')">
                                            <i class="bx bx-check"></i> Selesaikan
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Data produksi kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
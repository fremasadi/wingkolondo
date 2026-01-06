@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    {{-- <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Retur</h4>
        <a href="{{ route('returs.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Retur
        </a>
    </div> --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Toko</th>
                        <th>Tanggal Retur</th>
                        <th>Total</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returs as $retur)
                        <tr>
                            <td>{{ $retur->distribusi->pesanan->toko->nama_toko }}</td>
                            <td>{{ $retur->tanggal_retur }}</td>
                            <td>Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('returs.show', $retur->id) }}" class="btn btn-sm btn-info">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Data retur kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-xxl">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Data Retur</h5>
            <a href="{{ route('returs.create') }}" class="btn btn-primary">Tambah Retur</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Toko</th>
                        <th>Tanggal Retur</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returs as $retur)
                    <tr>
                        <td>{{ $retur->distribusi->pesanan->toko->nama_toko }}</td>
                        <td>{{ $retur->tanggal_retur }}</td>
                        <td>Rp {{ number_format($retur->total_retur) }}</td>
                        <td>
                            <a href="{{ route('returs.show', $retur->id) }}" class="btn btn-sm btn-info">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
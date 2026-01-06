@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Piutang</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Toko</th>
                        <th>Pesanan</th>
                        <th>Total</th>
                        <th>Sisa</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($piutangs as $p)
                    <tr>
                        <td>{{ $p->toko->nama_toko }}</td>
                        <td>#{{ $p->pesanan_id }}</td>
                        <td>Rp {{ number_format($p->total_tagihan) }}</td>
                        <td>Rp {{ number_format($p->sisa_tagihan) }}</td>
                        <td>{{ $p->jatuh_tempo ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status == 'lunas' ? 'success' : 'warning' }}">
                                {{ strtoupper($p->status) }}
                            </span>
                        </td>
                        <td>
                            @if($p->status == 'belum_lunas')
                            <a href="{{ route('piutangs.edit', $p) }}"
                               class="btn btn-sm btn-primary">
                                DiBayar
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            Data piutang kosong
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Retur</h4>
        <a href="{{ route('returs.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Retur
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Toko</th>
                        <th>Jadwal Ambil</th>
                        <th>Kurir</th>
                        <th>Status</th>
                        <th>Refund</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returs as $retur)
                        @php
                            $statusClass = [
                                'ditugaskan' => 'warning',
                                'dijemput' => 'primary',
                                'selesai' => 'success',
                                'batal' => 'danger',
                            ][$retur->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $retur->distribusi->pesanan->order_code ?? '#' . $retur->distribusi->pesanan->id }}</strong>
                                <div class="small text-muted">{{ optional($retur->tanggal_retur)->format('Y-m-d') }}</div>
                            </td>
                            <td>{{ $retur->distribusi->pesanan->toko->nama_toko }}</td>
                            <td>{{ optional($retur->tanggal_pengambilan)->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ $retur->kurir->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-{{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $retur->status)) }}
                                </span>
                            </td>
                            <td>
                                Rp {{ number_format($retur->total_refund ?: $retur->total_retur, 0, ',', '.') }}
                                <div class="small text-muted">{{ ucfirst(str_replace('_', ' ', $retur->refund_method)) }}</div>
                            </td>
                            <td>
                                @if($retur->status === 'dijemput')
                                    <form action="{{ route('returs.approve', $retur) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-success" onclick="return confirm('ACC retur ini sebagai selesai?')">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('returs.show', $retur->id) }}" class="btn btn-sm btn-info">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Data retur kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

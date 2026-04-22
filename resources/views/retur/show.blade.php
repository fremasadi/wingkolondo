@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Retur</h5>
            @if($retur->status === 'dijemput')
                <form action="{{ route('returs.approve', $retur) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success btn-sm" onclick="return confirm('ACC retur ini sebagai selesai?')">
                        <i class="bx bx-check"></i> ACC Selesai
                    </button>
                </form>
            @endif
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <strong>Order:</strong> {{ $retur->distribusi->pesanan->order_code ?? '#' . $retur->distribusi->pesanan->id }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Toko:</strong> {{ $retur->distribusi->pesanan->toko->nama_toko }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Kurir:</strong> {{ $retur->kurir->name ?? '-' }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Tanggal Retur:</strong> {{ optional($retur->tanggal_retur)->format('Y-m-d') }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Jadwal Ambil:</strong> {{ optional($retur->tanggal_pengambilan)->format('Y-m-d') ?? '-' }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $retur->status)) }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Metode Refund:</strong> {{ ucfirst(str_replace('_', ' ', $retur->refund_method)) }}
                </div>
                <div class="col-md-8 mb-2">
                    <strong>Alasan:</strong> {{ $retur->alasan ?? '-' }}
                </div>
            </div>

            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Kondisi</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retur->details as $d)
                        <tr>
                            <td>{{ $d->produk->nama_produk }}</td>
                            <td>{{ $d->qty }}</td>
                            <td>{{ ucfirst($d->kondisi) }}</td>
                            <td>Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h5 class="mt-3">Total Refund: Rp {{ number_format($retur->total_refund ?: $retur->total_retur, 0, ',', '.') }}</h5>

            @if($retur->pickup_photo || $retur->pickup_latitude || $retur->pickup_longitude || $retur->pickup_note)
                <hr>
                <h6 class="mb-3">Bukti Pickup Kurir</h6>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Waktu Pickup:</strong> {{ optional($retur->picked_up_at)->format('Y-m-d H:i:s') ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Koordinat:</strong> {{ $retur->pickup_latitude ?? '-' }}, {{ $retur->pickup_longitude ?? '-' }}
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong>Catatan Kurir:</strong> {{ $retur->pickup_note ?? '-' }}
                    </div>
                    @if($retur->pickup_photo)
                        <div class="col-md-12">
                            <img src="{{ asset('storage/' . $retur->pickup_photo) }}" alt="Bukti pickup retur" class="img-fluid rounded" style="max-width: 320px;">
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

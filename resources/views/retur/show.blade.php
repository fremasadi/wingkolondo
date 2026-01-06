@extends('layouts.app')

@section('content')
    <div class="container-xxl">
        <div class="card">
            <div class="card-header">
                <h5>Detail Retur</h5>
            </div>

            <div class="card-body">
                <p><strong>Toko:</strong> {{ $retur->distribusi->pesanan->toko->nama_toko }}</p>
                <p><strong>Tanggal:</strong> {{ $retur->tanggal_retur }}</p>

                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($retur->details as $d)
                            <tr>
                                <td>{{ $d->produk->nama_produk }}</td>
                                <td>{{ $d->qty }}</td>
                                <td>Rp {{ number_format($d->harga) }}</td>
                                <td>Rp {{ number_format($d->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h5 class="mt-3">Total Retur: Rp {{ number_format($retur->total_retur) }}</h5>
            </div>
        </div>
    </div>
@endsection

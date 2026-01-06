@extends('layouts.app')

@section('content')
    <div class="container-xxl">
        <div class="card">
            <div class="card-header">
                <h5>Detail Produksi</h5>
            </div>

            <div class="card-body">
                <p><strong>Tanggal:</strong> {{ $produksi->tanggal_produksi }}</p>
                <p><strong>Status:</strong> {{ ucfirst($produksi->status) }}</p>

                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produksi->details as $d)
                            <tr>
                                <td>{{ $d->produk->nama_produk }}</td>
                                <td>{{ $d->qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

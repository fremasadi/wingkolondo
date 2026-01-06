@extends('layouts.app')

@section('content')
    <div class="container-xxl">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Data Produksi</h5>
                <a href="{{ route('produksis.create') }}" class="btn btn-primary">
                    Tambah Produksi
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Detail</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produksis as $p)
                            <tr>
                                <td>{{ $p->tanggal_produksi }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $p->status === 'selesai' ? 'success' : 'warning' }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td>
                                    @foreach ($p->details as $d)
                                        {{ $d->produk->nama_produk }} ({{ $d->qty }})<br>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('produksis.show', $p->id) }}" class="btn btn-sm btn-info">
                                        Detail
                                    </a>

                                    @if ($p->status !== 'selesai')
                                        <form action="{{ route('produksis.selesai', $p->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Selesaikan produksi?')">
                                                Selesaikan
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

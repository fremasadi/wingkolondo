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
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
                            <span class="badge bg-label-{{ $distribusi->status_pengiriman === 'selesai' ? 'success' : ($distribusi->status_pengiriman === 'terkirim' ? 'primary' : 'warning') }}">
                                {{ ucfirst($distribusi->status_pengiriman) }}
                            </span>
                            @if($distribusi->delivered_at)
                                <div class="small text-muted mt-1">
                                    {{ $distribusi->delivered_at->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($distribusi->status_pengiriman === 'terkirim')

                                <form action="{{ route('distribusis.selesai', $distribusi) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-success"
                                            onclick="return confirm('ACC distribusi ini sebagai selesai?')">
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

    @foreach($distribusis as $distribusi)
        @if($distribusi->delivery_photo || $distribusi->delivery_latitude || $distribusi->delivery_longitude || $distribusi->delivery_note)
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-2">Bukti Pengiriman: {{ $distribusi->pesanan->toko->nama_toko }}</h6>
                    <div class="small text-muted mb-2">Status: {{ ucfirst($distribusi->status_pengiriman) }}</div>
                    @if($distribusi->delivered_at)
                        <div class="mb-1">Waktu konfirmasi: {{ $distribusi->delivered_at->format('Y-m-d H:i:s') }}</div>
                    @endif
                    @if($distribusi->delivery_latitude && $distribusi->delivery_longitude)
                        <div class="mb-1">Koordinat: {{ $distribusi->delivery_latitude }}, {{ $distribusi->delivery_longitude }}</div>
                    @endif
                    @if($distribusi->delivery_note)
                        <div class="mb-2">Catatan kurir: {{ $distribusi->delivery_note }}</div>
                    @endif
                    @if($distribusi->delivery_photo)
                        <img src="{{ asset('storage/' . $distribusi->delivery_photo) }}" alt="Bukti pengiriman" class="img-fluid rounded" style="max-width: 260px;">
                    @endif
                </div>
            </div>
        @endif
    @endforeach

</div>
@endsection

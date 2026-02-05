@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    @php
        $isDistribusiView = request()->query('view') == 'distribusi';
    @endphp

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">{{ $isDistribusiView ? 'Distribusi Pesanan' : 'Data Pesanan' }}</h4>
        @if(!$isDistribusiView)
        <a href="{{ route('pesanans.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Pesanan
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        @if($isDistribusiView)
                            <th>Tgl Kirim</th>
                            <th>Toko</th>
                            <th>Kurir</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        @else
                            <th>Tanggal</th>
                            <th>Toko</th>
                            <th>Status</th>
                            <th>Distribusi</th>
                            <th>Total</th>
                            <th width="120">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($isDistribusiView)
                        @php
                            $distribusiPesanans = $pesanans->filter(fn($p) => $p->distribusi);
                        @endphp
                        @forelse($distribusiPesanans as $pesanan)
                        <tr>
                            <td>{{ $pesanan->distribusi->tanggal_kirim }}</td>
                            <td>{{ $pesanan->toko->nama_toko }}</td>
                            <td>{{ $pesanan->distribusi->kurir->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-{{ $pesanan->distribusi->status_pengiriman == 'selesai' ? 'success' : 'warning' }}">
                                    {{ ucfirst($pesanan->distribusi->status_pengiriman) }}
                                </span>
                            </td>
                            <td>
                                @if($pesanan->distribusi->status_pengiriman !== 'selesai')
                                    <form action="{{ route('pesanans.distribusi.selesai', $pesanan) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Tandai distribusi selesai?')">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('pesanans.edit', $pesanan) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bx bx-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada distribusi</td>
                        </tr>
                        @endforelse
                    @else
                        @forelse($pesanans as $pesanan)
                        <tr>
                            <td>{{ $pesanan->tanggal_pesanan }}</td>
                            <td>{{ $pesanan->toko->nama_toko }}</td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ ucfirst($pesanan->status_pesanan) }}
                                </span>
                            </td>
                            <td>
                                @if($pesanan->distribusi)
                                    <span class="badge bg-label-{{ $pesanan->distribusi->status_pengiriman == 'selesai' ? 'success' : 'warning' }}">
                                        {{ ucfirst($pesanan->distribusi->status_pengiriman) }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Belum</span>
                                @endif
                            </td>
                            <td>{{ $pesanan->total_harga_formatted }}</td>
                            <td>
                                <a href="{{ route('pesanans.edit', $pesanan) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form action="{{ route('pesanans.destroy', $pesanan) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus pesanan?')"
                                            class="btn btn-sm btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Data pesanan kosong</td>
                        </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
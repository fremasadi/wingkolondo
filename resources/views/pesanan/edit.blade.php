@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-3">Edit Pesanan</h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Data Pesanan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pesanans.update', $pesanan) }}" method="POST">
                @csrf @method('PUT')
                @include('pesanan._form', ['button' => 'Update Pesanan'])
            </form>
        </div>
    </div>

    {{-- Distribusi Section --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Distribusi</h5>
            @if($pesanan->distribusi && $pesanan->distribusi->status_pengiriman === 'terkirim')
                <form action="{{ route('pesanans.distribusi.selesai', $pesanan) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-success btn-sm" onclick="return confirm('ACC distribusi ini sebagai selesai?')">
                        <i class="bx bx-check"></i> ACC Selesai
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($pesanan->distribusi)
                {{-- Edit Distribusi --}}
                <form action="{{ route('pesanans.distribusi.update', $pesanan) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kurir</label>
                            <select name="kurir_id" class="form-select">
                                <option value="">-- Pilih Kurir --</option>
                                @foreach($kurirs as $kurir)
                                    <option value="{{ $kurir->id }}"
                                        {{ $pesanan->distribusi->kurir_id == $kurir->id ? 'selected' : '' }}>
                                        {{ $kurir->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Kirim</label>
                            <input type="date" name="tanggal_kirim" class="form-control"
                                value="{{ $pesanan->tanggal_kirim }}" readonly required>
                            <small class="text-muted">Tanggal kirim mengikuti tanggal kirim di pesanan dan tidak dapat diubah manual.</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Pengiriman</label>
                            <select name="status_pengiriman" class="form-select">
                                @foreach(['pending','dikirim','terkirim','retur'] as $status)
                                    <option value="{{ $status }}"
                                        {{ $pesanan->distribusi->status_pengiriman == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control"
                                value="{{ $pesanan->distribusi->catatan }}">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Update Distribusi</button>
                        <button type="button" class="btn btn-danger" onclick="if(confirm('Hapus distribusi?')) document.getElementById('delete-distribusi').submit()">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                </form>

                @if($pesanan->distribusi->delivery_photo || $pesanan->distribusi->delivery_latitude || $pesanan->distribusi->delivery_longitude || $pesanan->distribusi->delivery_note)
                    <hr>
                    <h6 class="mb-3">Bukti Konfirmasi Kurir</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Waktu Konfirmasi:</strong>
                            {{ optional($pesanan->distribusi->delivered_at)->format('Y-m-d H:i:s') ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Koordinat:</strong>
                            {{ $pesanan->distribusi->delivery_latitude ?? '-' }},
                            {{ $pesanan->distribusi->delivery_longitude ?? '-' }}
                        </div>
                        <div class="col-md-12 mb-3">
                            <strong>Catatan Kurir:</strong>
                            {{ $pesanan->distribusi->delivery_note ?? '-' }}
                        </div>
                        @if($pesanan->distribusi->delivery_photo)
                            <div class="col-md-12">
                                <img src="{{ asset('storage/' . $pesanan->distribusi->delivery_photo) }}"
                                     alt="Bukti pengiriman"
                                     class="img-fluid rounded"
                                     style="max-width: 320px;">
                            </div>
                        @endif
                    </div>
                @endif
                <form id="delete-distribusi" action="{{ route('pesanans.distribusi.destroy', $pesanan) }}" method="POST" class="d-none">
                    @csrf @method('DELETE')
                </form>
            @else
                {{-- Create Distribusi --}}
                <form action="{{ route('pesanans.distribusi.store', $pesanan) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kurir</label>
                            <select name="kurir_id" class="form-select">
                                <option value="">-- Pilih Kurir --</option>
                                @foreach($kurirs as $kurir)
                                    <option value="{{ $kurir->id }}">{{ $kurir->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Kirim</label>
                            <input type="date" name="tanggal_kirim" class="form-control"
                                value="{{ $pesanan->tanggal_kirim }}" readonly required>
                            <small class="text-muted">Tanggal kirim mengikuti tanggal kirim di pesanan dan tidak dapat diubah manual.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-primary">Buat Distribusi</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

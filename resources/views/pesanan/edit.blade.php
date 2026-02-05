@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-3">Edit Pesanan</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
            @if($pesanan->distribusi && $pesanan->distribusi->status_pengiriman !== 'selesai')
                <form action="{{ route('pesanans.distribusi.selesai', $pesanan) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <button class="btn btn-success btn-sm" onclick="return confirm('Tandai distribusi selesai?')">
                        <i class="bx bx-check"></i> Selesaikan Distribusi
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
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
                                value="{{ $pesanan->distribusi->tanggal_kirim }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Pengiriman</label>
                            <select name="status_pengiriman" class="form-select">
                                @foreach(['pending','dikirim','selesai','retur'] as $status)
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
                                value="{{ $pesanan->tanggal_kirim ?? now()->toDateString() }}" required>
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
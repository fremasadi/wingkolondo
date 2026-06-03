@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Detail Pesanan</h4>
        <div class="d-flex gap-2">
            @if($pesanan->status_pesanan !== 'selesai' && $pesanan->distribusi?->status_pengiriman !== 'selesai')
                <a href="{{ route('pesanans.edit', $pesanan) }}" class="btn btn-warning">
                    <i class="bx bx-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('pesanans.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Pesanan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>Order:</strong> {{ $pesanan->order_code ?? '#' . $pesanan->id }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Toko:</strong> {{ $pesanan->toko->nama_toko ?? '-' }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Status:</strong>
                    <span class="badge bg-label-{{ $pesanan->status_pesanan === 'selesai' ? 'success' : 'info' }}">
                        {{ ucfirst($pesanan->status_pesanan) }}
                    </span>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Tanggal Pesanan:</strong> {{ $pesanan->tanggal_pesanan }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Tanggal Kirim:</strong> {{ $pesanan->tanggal_kirim ?? '-' }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Metode Pembayaran:</strong> {{ ucfirst($pesanan->metode_pembayaran) }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Total:</strong> {{ $pesanan->total_harga_formatted }}
                </div>
                @if($pesanan->piutang)
                    <div class="col-md-4 mb-3">
                        <strong>Piutang:</strong> Rp {{ number_format($pesanan->piutang->sisa_tagihan, 0, ',', '.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Detail Produk</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th width="110">Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan->details as $detail)
                        <tr>
                            <td>{{ $detail->produk->nama_produk ?? '-' }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada detail produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Distribusi</h5>
        </div>
        <div class="card-body">
            @if($pesanan->distribusi)
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Kurir:</strong> {{ $pesanan->distribusi->kurir->name ?? '-' }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Tanggal Kirim:</strong> {{ $pesanan->distribusi->tanggal_kirim }}
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Status:</strong>
                        <span class="badge bg-label-{{ $pesanan->distribusi->status_pengiriman === 'selesai' ? 'success' : ($pesanan->distribusi->status_pengiriman === 'terkirim' ? 'primary' : 'warning') }}">
                            {{ ucfirst($pesanan->distribusi->status_pengiriman) }}
                        </span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong>Catatan:</strong> {{ $pesanan->distribusi->catatan ?? '-' }}
                    </div>
                    @if($pesanan->distribusi->delivered_at || $pesanan->distribusi->delivery_note || $pesanan->distribusi->delivery_photo)
                        <div class="col-md-4 mb-3">
                            <strong>Waktu Konfirmasi:</strong> {{ optional($pesanan->distribusi->delivered_at)->format('Y-m-d H:i:s') ?? '-' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Koordinat:</strong> {{ $pesanan->distribusi->delivery_latitude ?? '-' }}, {{ $pesanan->distribusi->delivery_longitude ?? '-' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Disetujui Oleh:</strong> {{ $pesanan->distribusi->approver->name ?? '-' }}
                        </div>
                        <div class="col-md-12 mb-3">
                            <strong>Catatan Kurir:</strong> {{ $pesanan->distribusi->delivery_note ?? '-' }}
                        </div>
                        @if($pesanan->distribusi->delivery_photo)
                            <div class="col-md-12">
                                <img src="{{ asset('storage/' . $pesanan->distribusi->delivery_photo) }}"
                                     alt="Bukti pengiriman"
                                     class="img-fluid rounded"
                                     style="max-width: 320px;">
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="alert alert-warning mb-0">Belum ada data distribusi untuk pesanan ini.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            <i class="bx bx-trash-alt text-danger me-2"></i>Riwayat Pembuangan Stok Kadaluarsa
        </h4>
        <form method="POST" action="{{ route('pembuangan-stok.otomatis') }}" class="d-inline"
              onsubmit="return confirm('Jalankan pembuangan otomatis untuk semua produk yang sudah kadaluarsa sekarang?')">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="bx bx-refresh me-1"></i> Jalankan Pembuangan Otomatis
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="bx bx-info-circle me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Qty Dibuang</th>
                        <th>Tanggal Buang</th>
                        <th>Metode</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembuangans as $p)
                    <tr>
                        <td>{{ $pembuangans->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('produks.show', $p->produk) }}" class="fw-bold text-dark">
                                {{ $p->produk->nama_produk ?? '-' }}
                            </a>
                        </td>
                        <td><span class="text-danger fw-bold">{{ $p->qty }} pcs</span></td>
                        <td>{{ $p->tanggal_buang->format('d/m/Y') }}</td>
                        <td>
                            @if($p->metode === 'otomatis')
                                <span class="fw-bold text-info"><i class="bx bx-refresh"></i> Otomatis</span>
                            @else
                                <span class="fw-bold text-warning"><i class="bx bx-user"></i> Manual</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:13px;">{{ $p->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bx bx-box" style="font-size:2rem;"></i>
                            <p class="mt-2">Belum ada riwayat pembuangan stok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pembuangans->hasPages())
        <div class="card-footer">
            {{ $pembuangans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

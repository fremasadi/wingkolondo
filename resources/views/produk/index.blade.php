@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between mb-3">
        <h4 class="fw-bold">Data Produk</h4>
        <a href="{{ route('produks.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Stok</th>
                        <th>Harga (per pcs)</th>
                        <th>Kadaluarsa</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $produk)
                    <tr>
                        <td>
                            {{ $produk->nama_produk }}
                            @if($produk->detail_produksis_count > 0)
                                <span class="text-info fw-bold ms-1" title="Digunakan dalam {{ $produk->detail_produksis_count }} produksi">
                                    <i class="bx bx-box"></i> {{ $produk->detail_produksis_count }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($produk->stok <= 0)
                                <span class="text-danger fw-bold">{{ $produk->stok }}</span>
                            @elseif($produk->stok <= 10)
                                <span class="text-warning fw-bold">{{ $produk->stok }}</span>
                            @else
                                <span class="text-success fw-bold">{{ $produk->stok }}</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                        <td>
                            @php $exp = $produk->tanggal_kadaluarsa; @endphp
                            @if(!$exp)
                                <span class="text-muted">—</span>
                            @elseif($exp->isPast())
                                <span class="text-danger fw-bold" title="Sudah kadaluarsa">
                                    <i class="bx bx-error-circle"></i> {{ $exp->format('d/m/Y') }}
                                </span>
                            @elseif($exp->diffInDays(now()) <= 7)
                                <span class="text-warning fw-bold" title="Hampir kadaluarsa">
                                    <i class="bx bx-time-five"></i> {{ $exp->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-success fw-bold">
                                    <i class="bx bx-check-circle"></i> {{ $exp->format('d/m/Y') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('produks.show', $produk) }}" class="btn btn-sm btn-info">
                                <i class="bx bx-show"></i>
                            </a>
                            <a href="{{ route('produks.edit', $produk) }}" class="btn btn-sm btn-warning">
                                <i class="bx bx-edit"></i>
                            </a>

                            @if($produk->detail_produksis_count == 0 && $produk->detail_returs_count == 0)
                                <form action="{{ route('produks.destroy', $produk) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus produk ini?')" class="btn btn-sm btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat dihapus karena sudah digunakan">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Data produk belum tersedia</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
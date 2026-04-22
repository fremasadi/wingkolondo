@extends('layouts.app')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="mb-3">
            <h4 class="fw-bold">Pembayaran Piutang</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('piutangs.update', $piutang) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Toko</label>
                        <input class="form-control" value="{{ $piutang->toko->nama_toko }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Tagihan</label>
                        <input class="form-control" value="Rp {{ number_format($piutang->total_tagihan) }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Dibayar Toko</label>
                        <input type="number" name="total_dibayar" min="0" max="{{ $piutang->total_tagihan }}"
                            class="form-control"
                            value="{{ old('total_dibayar', $piutang->total_tagihan - $piutang->sisa_tagihan) }}" required>
                        <small class="text-muted">
                            Isi sesuai nominal yang sudah dibayar toko
                        </small>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-success">Simpan</button>
                        <a href="{{ route('piutangs.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
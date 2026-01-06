@extends('layouts.app')

@section('content')
    <div class="container-xxl">
        <form method="POST" action="{{ route('returs.store') }}">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h5>Input Retur</h5>
                </div>

                <div class="card-body row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Distribusi</label>
                        <select name="distribusi_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($distribusis as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->pesanan->toko->nama_toko }} | {{ $d->tanggal_kirim }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Retur</label>
                        <input type="date" name="tanggal_retur" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <hr>
                        <h6>Detail Retur</h6>
                    </div>

                    <div class="col-md-5 mb-3">
                        <select name="produk_id[]" class="form-select" required>
                            @foreach (\App\Models\Produk::all() as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button class="btn btn-primary">Simpan Retur</button>
                    </div>

                </div>
            </div>

        </form>
    </div>
@endsection

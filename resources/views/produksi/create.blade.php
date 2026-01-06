@extends('layouts.app')

@section('content')
    <div class="container-xxl">
        <form method="POST" action="{{ route('produksis.store') }}">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h5>Tambah Produksi</h5>
                </div>

                <div class="card-body row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Produksi</label>
                        <input type="date" name="tanggal_produksi" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <hr>
                        <h6>Detail Produksi</h6>
                    </div>

                    <div class="col-md-5 mb-3">
                        <select name="produk_id[]" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produks as $produk)
                                <option value="{{ $produk->id }}">
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <input type="number" name="qty[]" class="form-control" min="1" placeholder="Qty" required>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button class="btn btn-primary">
                            Simpan Produksi
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

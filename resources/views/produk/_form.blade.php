<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Produk</label>
        <input type="text" name="nama_produk" class="form-control"
            value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Stok Awal</label>
        <input type="number" name="stok" min="0" class="form-control"
            value="{{ old('stok', $produk->stok ?? 0) }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Harga <small class="text-muted">(per pcs)</small></label>
        <input type="number" name="harga" min="0" class="form-control"
            value="{{ old('harga', $produk->harga ?? 0) }}" required>
    </div>
</div>

<hr>

<h6 class="mt-3">Komposisi Bahan Baku (per 1 pcs)</h6>

@foreach($bahanBakus as $bahan)
<div class="row align-items-center mb-2">
    <div class="col-md-5">
        <label>{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</label>
    </div>
    <div class="col-md-4">
        <input type="number" step="0.01"
            name="bahan[{{ $bahan->id }}]"
            class="form-control"
            placeholder="Qty per pcs"
            value="{{ old('bahan.'.$bahan->id, $resep[$bahan->id] ?? '') }}">
    </div>
</div>
@endforeach

<div class="mt-4">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('produks.index') }}" class="btn btn-secondary">Kembali</a>
</div>
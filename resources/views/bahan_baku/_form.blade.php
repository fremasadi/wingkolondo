<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Bahan</label>
        <input type="text" name="nama_bahan" class="form-control"
            value="{{ old('nama_bahan', $bahanBaku->nama_bahan ?? '') }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" min="0" class="form-control"
            value="{{ old('stok', $bahanBaku->stok ?? 0) }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Satuan</label>
        <select name="satuan" class="form-select" required>
            <option value="">-- Pilih Satuan --</option>
            <option value="kg" @selected(old('satuan', $bahanBaku->satuan ?? '') == 'kg')>Kg</option>
            <option value="gram" @selected(old('satuan', $bahanBaku->satuan ?? '') == 'gram')>Gram</option>
            <option value="liter" @selected(old('satuan', $bahanBaku->satuan ?? '') == 'liter')>Liter</option>
            <option value="pcs" @selected(old('satuan', $bahanBaku->satuan ?? '') == 'pcs')>Pcs</option>
            <option value="pack" @selected(old('satuan', $bahanBaku->satuan ?? '') == 'pack')>Pack</option>
        </select>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('bahan-bakus.index') }}" class="btn btn-secondary">Kembali</a>
</div>
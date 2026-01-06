<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control"
            value="{{ old('nama_toko', $toko->nama_toko ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">No HP</label>
        <input type="text" name="no_hp" class="form-control"
            value="{{ old('no_hp', $toko->no_hp ?? '') }}">
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $toko->alamat ?? '') }}</textarea>
    </div>
</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('tokos.index') }}" class="btn btn-secondary">Kembali</a>
</div>
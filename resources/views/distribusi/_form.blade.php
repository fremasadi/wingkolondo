<div class="row">

    {{-- Pesanan --}}
    @isset($pesanans)
    <div class="col-md-4 mb-3">
        <label class="form-label">Pesanan</label>
        <select name="pesanan_id" class="form-select" required>
            <option value="">-- Pilih Pesanan --</option>
            @foreach($pesanans as $pesanan)
                <option value="{{ $pesanan->id }}">
                    {{ $pesanan->toko->nama_toko }} |
                    {{ $pesanan->tanggal_pesanan }}
                </option>
            @endforeach
        </select>
    </div>
    @endisset

    {{-- Kurir --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">Kurir</label>
        <select name="kurir_id" class="form-select">
            <option value="">-- Pilih Kurir --</option>
            @foreach($kurirs as $kurir)
                <option value="{{ $kurir->id }}"
                    {{ old('kurir_id', $distribusi->kurir_id ?? '') == $kurir->id ? 'selected' : '' }}>
                    {{ $kurir->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tanggal --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Kirim</label>
        <input type="date" name="tanggal_kirim" class="form-control"
            value="{{ old('tanggal_kirim', $distribusi->tanggal_kirim ?? now()->toDateString()) }}" required>
    </div>

    {{-- Status (edit only) --}}
    @isset($distribusi)
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Pengiriman</label>
        <select name="status_pengiriman" class="form-select">
            @foreach(['pending','dikirim','selesai','retur'] as $status)
                <option value="{{ $status }}"
                    {{ $distribusi->status_pengiriman == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </div>
    @endisset

    {{-- Catatan --}}
    <div class="col-md-8 mb-3">
        <label class="form-label">Catatan</label>
        <textarea name="catatan" class="form-control"
            rows="2">{{ old('catatan', $distribusi->catatan ?? '') }}</textarea>
    </div>

</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('distribusis.index') }}" class="btn btn-secondary">Kembali</a>
</div>
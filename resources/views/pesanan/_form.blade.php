<div class="row">

    <div class="col-md-4 mb-3">
        <label class="form-label">Toko</label>
        <select name="toko_id" class="form-select" required>
            <option value="">-- Pilih Toko --</option>
            @foreach($tokos as $toko)
                <option value="{{ $toko->id }}"
                    {{ old('toko_id', $pesanan->toko_id ?? '') == $toko->id ? 'selected' : '' }}>
                    {{ $toko->nama_toko }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Pesanan</label>
        <input type="date" name="tanggal_pesanan" class="form-control"
            value="{{ old('tanggal_pesanan', $pesanan->tanggal_pesanan ?? now()->toDateString()) }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Kirim</label>
        <input type="date" name="tanggal_kirim" class="form-control"
            value="{{ old('tanggal_kirim', $pesanan->tanggal_kirim ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Metode Pembayaran</label>
        <select name="metode_pembayaran" class="form-select" required>
            <option value="cash"
                {{ old('metode_pembayaran', $pesanan->metode_pembayaran ?? 'tempo') == 'cash' ? 'selected' : '' }}>
                Cash
            </option>
            <option value="transfer"
                {{ old('metode_pembayaran', $pesanan->metode_pembayaran ?? '') == 'transfer' ? 'selected' : '' }}>
                Transfer
            </option>
            <option value="tempo"
                {{ old('metode_pembayaran', $pesanan->metode_pembayaran ?? '') == 'tempo' ? 'selected' : '' }}>
                Tempo / Piutang
            </option>
        </select>
    </div>

    @isset($pesanan)
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Pesanan</label>
        <select name="status_pesanan" class="form-select">
            @foreach(['draft','dikonfirmasi','diproses','dikirim','selesai','batal'] as $status)
                <option value="{{ $status }}"
                    {{ $pesanan->status_pesanan == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </div>
    @endisset

    <hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Detail Produk</h5>
    <button type="button" class="btn btn-sm btn-success" id="btn-tambah-item">
        <i class="bx bx-plus"></i> Tambah Item
    </button>
</div>

<table class="table" id="tabel-item">
    <thead>
        <tr>
            <th>Produk</th>
            <th width="120">Qty</th>
            <th width="60">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $details = old('produk_id') ?? ($pesanan->details ?? [null]);
        @endphp

        @foreach($details as $i => $detail)
        <tr>
            <td>
                <select name="produk_id[]" class="form-select" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id }}"
                            {{ old("produk_id.$i", $detail->produk_id ?? '') == $produk->id ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="qty[]" min="1" class="form-control"
                    value="{{ old("qty.$i", $detail->qty ?? 1) }}">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-hapus-item">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('pesanans.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<template id="template-item">
    <tr>
        <td>
            <select name="produk_id[]" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($produks as $produk)
                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="qty[]" min="1" class="form-control" value="1">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-hapus-item">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.getElementById('btn-tambah-item').addEventListener('click', function () {
    var template = document.getElementById('template-item');
    var clone = template.content.cloneNode(true);
    document.querySelector('#tabel-item tbody').appendChild(clone);
});

document.getElementById('tabel-item').addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-hapus-item');
    if (!btn) return;
    var rows = this.querySelectorAll('tbody tr');
    if (rows.length <= 1) {
        alert('Minimal harus ada 1 item produk');
        return;
    }
    btn.closest('tr').remove();
});
</script>
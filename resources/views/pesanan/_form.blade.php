<div class="row">

    @isset($pesanan)
    <div class="col-md-4 mb-3">
        <label class="form-label">Order ID</label>
        <input type="text" class="form-control" value="{{ $pesanan->order_code ?? '#' . $pesanan->id }}" readonly>
    </div>
    @endisset

    <div class="col-md-4 mb-3">
        <label class="form-label">Toko</label>
        <select name="toko_id" class="form-select @error('toko_id') is-invalid @enderror" required>
            <option value="">-- Pilih Toko --</option>
            @foreach($tokos as $toko)
                <option value="{{ $toko->id }}"
                    {{ old('toko_id', $pesanan->toko_id ?? '') == $toko->id ? 'selected' : '' }}>
                    {{ $toko->nama_toko }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback" data-error-for="toko_id">
            @error('toko_id') {{ $message }} @else Silakan pilih toko terlebih dahulu. @enderror
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Pesanan</label>
        <input type="date" name="tanggal_pesanan" class="form-control @error('tanggal_pesanan') is-invalid @enderror"
            value="{{ old('tanggal_pesanan', $pesanan->tanggal_pesanan ?? now()->toDateString()) }}" required>
        <div class="invalid-feedback" data-error-for="tanggal_pesanan">
            @error('tanggal_pesanan') {{ $message }} @else Tanggal pesanan wajib diisi. @enderror
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Kirim</label>
        <input type="date" name="tanggal_kirim" class="form-control @error('tanggal_kirim') is-invalid @enderror"
            min="{{ old('tanggal_pesanan', $pesanan->tanggal_pesanan ?? now()->toDateString()) }}"
            value="{{ old('tanggal_kirim', $pesanan->tanggal_kirim ?? '') }}">
        <div class="invalid-feedback" data-error-for="tanggal_kirim">
            @error('tanggal_kirim') {{ $message }} @else Tanggal kirim tidak boleh sebelum tanggal pesanan. @enderror
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Metode Pembayaran</label>
        <select name="metode_pembayaran" class="form-select @error('metode_pembayaran') is-invalid @enderror" required>
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
        <div class="invalid-feedback" data-error-for="metode_pembayaran">
            @error('metode_pembayaran') {{ $message }} @else Silakan pilih metode pembayaran. @enderror
        </div>
    </div>

    @isset($pesanan)
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Pesanan</label>
        <select name="status_pesanan" class="form-select @error('status_pesanan') is-invalid @enderror" required>
            @foreach(['draft','dikonfirmasi','diproses','dikirim','selesai','batal'] as $status)
                <option value="{{ $status }}"
                    {{ old('status_pesanan', $pesanan->status_pesanan) == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback" data-error-for="status_pesanan">
            @error('status_pesanan') {{ $message }} @else Silakan pilih status pesanan. @enderror
        </div>
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
<div class="text-danger small d-none" id="detail-item-error">Lengkapi semua produk dan qty minimal 1.</div>

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
document.addEventListener('DOMContentLoaded', function () {
    var btnTambahItem = document.getElementById('btn-tambah-item');
    var tabelItem = document.getElementById('tabel-item');
    var tokoSelect = document.querySelector('select[name="toko_id"]');
    var tanggalPesanan = document.querySelector('input[name="tanggal_pesanan"]');
    var tanggalKirim = document.querySelector('input[name="tanggal_kirim"]');
    var metodePembayaran = document.querySelector('select[name="metode_pembayaran"]');
    var statusPesanan = document.querySelector('select[name="status_pesanan"]');
    var detailItemError = document.getElementById('detail-item-error');
    var formPesanan = btnTambahItem ? btnTambahItem.closest('form') : null;

    function showAlert(icon, title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'OK'
            });
            return;
        }

        alert(text);
    }

    function syncTanggalKirimMin() {
        if (!tanggalPesanan || !tanggalKirim) {
            return true;
        }

        var tanggalPesanValue = tanggalPesanan.value;
        tanggalKirim.min = tanggalPesanValue;

        if (tanggalKirim.value && tanggalKirim.value < tanggalPesanValue) {
            tanggalKirim.classList.add('is-invalid');
            tanggalKirim.value = '';
            showAlert('warning', 'Tanggal kirim tidak valid', 'Tanggal kirim tidak boleh sebelum tanggal pesanan.');
            tanggalKirim.focus();
            return false;
        }

        tanggalKirim.classList.remove('is-invalid');
        return true;
    }

    function setFieldError(field, message) {
        if (!field) {
            return;
        }

        field.classList.add('is-invalid');

        var feedback = formPesanan ? formPesanan.querySelector('[data-error-for="' + field.name + '"]') : null;
        if (feedback) {
            feedback.textContent = message;
        }
    }

    function clearFieldError(field) {
        if (!field) {
            return;
        }

        field.classList.remove('is-invalid');
    }

    function validateRequiredField(field, message) {
        if (!field || field.disabled) {
            return true;
        }

        if (String(field.value || '').trim() === '') {
            setFieldError(field, message);
            return false;
        }

        clearFieldError(field);
        return true;
    }

    function validateDetailItems() {
        if (!tabelItem) {
            return true;
        }

        var rows = tabelItem.querySelectorAll('tbody tr');
        var valid = rows.length > 0;

        rows.forEach(function (row) {
            var produk = row.querySelector('select[name="produk_id[]"]');
            var qty = row.querySelector('input[name="qty[]"]');

            if (!produk || !qty) {
                return;
            }

            var rowValid = true;

            if (String(produk.value || '').trim() === '') {
                produk.classList.add('is-invalid');
                rowValid = false;
                valid = false;
            } else {
                produk.classList.remove('is-invalid');
            }

            if (String(qty.value || '').trim() === '' || Number(qty.value) < 1) {
                qty.classList.add('is-invalid');
                rowValid = false;
                valid = false;
            } else {
                qty.classList.remove('is-invalid');
            }

            if (!rowValid) {
                valid = false;
            }
        });

        if (detailItemError) {
            detailItemError.classList.toggle('d-none', valid);
        }

        return valid;
    }

    if (btnTambahItem) {
        btnTambahItem.addEventListener('click', function () {
            var template = document.getElementById('template-item');
            var clone = template.content.cloneNode(true);
            document.querySelector('#tabel-item tbody').appendChild(clone);
        });
    }

    if (tabelItem) {
        tabelItem.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-hapus-item');
            if (!btn) return;

            var rows = this.querySelectorAll('tbody tr');
            if (rows.length <= 1) {
                showAlert('warning', 'Item produk kurang', 'Minimal harus ada 1 item produk.');
                return;
            }

            btn.closest('tr').remove();
            validateDetailItems();
        });
    }

    if (tanggalPesanan) {
        tanggalPesanan.addEventListener('change', function () {
            clearFieldError(tanggalPesanan);
            syncTanggalKirimMin();
        });
    }

    if (tanggalKirim) {
        tanggalKirim.addEventListener('change', function () {
            clearFieldError(tanggalKirim);
            syncTanggalKirimMin();
        });
    }

    [tokoSelect, metodePembayaran, statusPesanan].forEach(function (field) {
        if (!field) {
            return;
        }

        field.addEventListener('change', function () {
            clearFieldError(field);
        });
    });

    if (tabelItem) {
        tabelItem.addEventListener('change', function (e) {
            if (e.target.matches('select[name="produk_id[]"], input[name="qty[]"]')) {
                e.target.classList.remove('is-invalid');
                validateDetailItems();
            }
        });
    }

    if (formPesanan) {
        formPesanan.addEventListener('submit', function (e) {
            var isValid = true;

            isValid = validateRequiredField(tokoSelect, 'Silakan pilih toko terlebih dahulu.') && isValid;
            isValid = validateRequiredField(tanggalPesanan, 'Tanggal pesanan wajib diisi.') && isValid;
            isValid = validateRequiredField(metodePembayaran, 'Silakan pilih metode pembayaran.') && isValid;
            isValid = validateRequiredField(statusPesanan, 'Silakan pilih status pesanan.') && isValid;
            isValid = validateDetailItems() && isValid;
            isValid = syncTanggalKirimMin() && isValid;

            if (!isValid) {
                e.preventDefault();
                showAlert('warning', 'Form belum lengkap', 'Lengkapi data pesanan terlebih dahulu sebelum menyimpan.');
            }
        });
    }

    validateDetailItems();
    syncTanggalKirimMin();
});
</script>

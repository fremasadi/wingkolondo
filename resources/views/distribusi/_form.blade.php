<div class="row">

    {{-- Pesanan --}}
    @isset($pesanans)
    <div class="col-md-4 mb-3">
        <label class="form-label">Pesanan</label>
        <select name="pesanan_id" class="form-select @error('pesanan_id') is-invalid @enderror" required>
            <option value="">-- Pilih Pesanan --</option>
            @foreach($pesanans as $pesanan)
                <option value="{{ $pesanan->id }}"
                    data-tanggal-kirim="{{ $pesanan->tanggal_kirim }}"
                    {{ old('pesanan_id', $distribusi->pesanan_id ?? '') == $pesanan->id ? 'selected' : '' }}>
                    {{ $pesanan->toko->nama_toko }} |
                    {{ $pesanan->tanggal_pesanan }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback" data-error-for="pesanan_id">
            @error('pesanan_id') {{ $message }} @else Silakan pilih pesanan terlebih dahulu. @enderror
        </div>
    </div>
    @endisset

    {{-- Kurir --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">Kurir</label>
        <select name="kurir_id" class="form-select @error('kurir_id') is-invalid @enderror" required>
            <option value="">-- Pilih Kurir --</option>
            @foreach($kurirs as $kurir)
                <option value="{{ $kurir->id }}"
                    {{ old('kurir_id', $distribusi->kurir_id ?? '') == $kurir->id ? 'selected' : '' }}>
                    {{ $kurir->name }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback" data-error-for="kurir_id">
            @error('kurir_id') {{ $message }} @else Silakan pilih kurir terlebih dahulu. @enderror
        </div>
    </div>

    {{-- Tanggal --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Kirim</label>
        <input type="date" name="tanggal_kirim" class="form-control @error('tanggal_kirim') is-invalid @enderror"
            value="{{ old('tanggal_kirim', $distribusi->pesanan->tanggal_kirim ?? $distribusi->tanggal_kirim ?? now()->toDateString()) }}" readonly required>
        <small class="text-muted">Tanggal kirim mengikuti tanggal kirim di pesanan dan tidak dapat diubah manual.</small>
        <div class="invalid-feedback d-block" data-error-for="tanggal_kirim">
            @error('tanggal_kirim') {{ $message }} @enderror
        </div>
    </div>

    {{-- Status (edit only) --}}
    @isset($distribusi)
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Pengiriman</label>
        <select name="status_pengiriman" class="form-select @error('status_pengiriman') is-invalid @enderror" required>
            @if(($distribusi->status_pengiriman ?? null) === 'selesai')
                <option value="selesai" selected>Selesai</option>
            @else
                @foreach(['pending','dikirim','terkirim'] as $status)
                    <option value="{{ $status }}"
                        {{ old('status_pengiriman', $distribusi->status_pengiriman) == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            @endif
        </select>
        <div class="invalid-feedback" data-error-for="status_pengiriman">
            @error('status_pengiriman') {{ $message }} @else Silakan pilih status pengiriman. @enderror
        </div>
    </div>
    @endisset

    {{-- Catatan --}}
    <div class="col-md-8 mb-3">
        <label class="form-label">Catatan</label>
        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror"
            rows="2">{{ old('catatan', $distribusi->catatan ?? '') }}</textarea>
        <div class="invalid-feedback">
            @error('catatan') {{ $message }} @enderror
        </div>
    </div>

</div>

<div class="mt-3">
    <button class="btn btn-primary">{{ $button }}</button>
    <a href="{{ route('distribusis.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var pesananSelect = document.querySelector('select[name="pesanan_id"]');
    var kurirSelect = document.querySelector('select[name="kurir_id"]');
    var statusSelect = document.querySelector('select[name="status_pengiriman"]');
    var tanggalKirim = document.querySelector('input[name="tanggal_kirim"]');
    var formDistribusi = tanggalKirim ? tanggalKirim.closest('form') : null;
    var fallbackTanggalKirim = @json(old('tanggal_kirim', $distribusi->pesanan->tanggal_kirim ?? $distribusi->tanggal_kirim ?? null));

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

    function getTanggalKirimPesanan() {
        if (pesananSelect) {
            var selectedOption = pesananSelect.options[pesananSelect.selectedIndex];
            return selectedOption ? selectedOption.dataset.tanggalKirim || '' : '';
        }

        return fallbackTanggalKirim || '';
    }

    function setFieldError(field, message) {
        if (!field) {
            return;
        }

        field.classList.add('is-invalid');

        var feedback = formDistribusi ? formDistribusi.querySelector('[data-error-for="' + field.name + '"]') : null;
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

    function syncTanggalKirim(forceValue) {
        if (!tanggalKirim) {
            return true;
        }

        var tanggalPesanan = getTanggalKirimPesanan();
        tanggalKirim.min = tanggalPesanan;

        if (!tanggalPesanan) {
            return true;
        }

        if (forceValue || !tanggalKirim.value || tanggalKirim.value < tanggalPesanan) {
            tanggalKirim.value = tanggalPesanan;
        }

        if (tanggalKirim.value < tanggalPesanan) {
            setFieldError(tanggalKirim, 'Tanggal kirim harus mengikuti tanggal kirim pada pesanan.');
            showAlert('warning', 'Tanggal kirim tidak valid', 'Tanggal kirim mengikuti tanggal kirim pada pesanan.');
            tanggalKirim.focus();
            return false;
        }

        clearFieldError(tanggalKirim);
        return true;
    }

    if (pesananSelect) {
        pesananSelect.addEventListener('change', function () {
            clearFieldError(pesananSelect);
            syncTanggalKirim(true);
        });
    }

    if (kurirSelect) {
        kurirSelect.addEventListener('change', function () {
            clearFieldError(kurirSelect);
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            clearFieldError(statusSelect);
        });
    }

    if (formDistribusi) {
        formDistribusi.addEventListener('submit', function (e) {
            var isValid = true;

            isValid = validateRequiredField(pesananSelect, 'Silakan pilih pesanan terlebih dahulu.') && isValid;
            isValid = validateRequiredField(kurirSelect, 'Silakan pilih kurir terlebih dahulu.') && isValid;
            isValid = validateRequiredField(statusSelect, 'Silakan pilih status pengiriman.') && isValid;
            isValid = syncTanggalKirim(false) && isValid;

            if (!isValid) {
                e.preventDefault();
                showAlert('warning', 'Form belum lengkap', 'Lengkapi field yang wajib diisi sebelum menyimpan distribusi.');
            }
        });
    }

    syncTanggalKirim(false);
});
</script>

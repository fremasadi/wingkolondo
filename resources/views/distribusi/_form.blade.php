<div class="row">

    {{-- Pesanan --}}
    @isset($pesanans)
    <div class="col-md-4 mb-3">
        <label class="form-label">Pesanan</label>
        <select name="pesanan_id" class="form-select" required>
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
            value="{{ old('tanggal_kirim', $distribusi->pesanan->tanggal_kirim ?? $distribusi->tanggal_kirim ?? now()->toDateString()) }}" readonly required>
        <small class="text-muted">Tanggal kirim mengikuti tanggal kirim di pesanan dan tidak dapat diubah manual.</small>
    </div>

    {{-- Status (edit only) --}}
    @isset($distribusi)
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Pengiriman</label>
        <select name="status_pengiriman" class="form-select">
            @if(($distribusi->status_pengiriman ?? null) === 'selesai')
                <option value="selesai" selected>Selesai</option>
            @else
                @foreach(['pending','dikirim','terkirim'] as $status)
                    <option value="{{ $status }}"
                        {{ $distribusi->status_pengiriman == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            @endif
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var pesananSelect = document.querySelector('select[name="pesanan_id"]');
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
            showAlert('warning', 'Tanggal kirim tidak valid', 'Tanggal kirim mengikuti tanggal kirim pada pesanan.');
            tanggalKirim.focus();
            return false;
        }

        return true;
    }

    if (pesananSelect) {
        pesananSelect.addEventListener('change', function () {
            syncTanggalKirim(true);
        });
    }

    if (formDistribusi) {
        formDistribusi.addEventListener('submit', function (e) {
            if (!syncTanggalKirim(false)) {
                e.preventDefault();
            }
        });
    }

    syncTanggalKirim(false);
});
</script>

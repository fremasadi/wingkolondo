@extends('layouts.app')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container-xxl container-p-y">
    <h4 class="fw-bold mb-3">Buat Tugas Retur</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('returs.store') }}">
        @csrf

        <div class="card">
            <div class="card-body row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Order Selesai</label>

                    {{-- Hidden real select for form submission --}}
                    <select name="distribusi_id" id="distribusi_id" style="display:none" required>
                        <option value="">-- Pilih Order --</option>
                        @foreach ($distribusis as $d)
                            @php
                                $items = $d->pesanan->details->map(function ($detail) {
                                    return [
                                        'produk_id' => $detail->produk_id,
                                        'nama_produk' => $detail->produk->nama_produk,
                                        'qty' => $detail->qty,
                                        'harga' => $detail->harga,
                                    ];
                                })->values();
                                $piutangStatus = $d->pesanan->piutang->status ?? '';
                                $canPotongPiutang = $piutangStatus === 'lunas';
                            @endphp
                            <option value="{{ $d->id }}"
                                data-toko-id="{{ $d->pesanan->toko_id }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($d->tanggal_kirim)->format('Y-m-d') }}"
                                data-items='@json($items)'
                                data-can-potong-piutang="{{ $canPotongPiutang ? '1' : '0' }}"
                                data-piutang-status="{{ $piutangStatus ?: 'tidak_ada' }}">
                                {{ $d->pesanan->order_code ?? '#' . $d->pesanan->id }} | {{ $d->pesanan->toko->nama_toko }} | Tgl Kirim: {{ \Carbon\Carbon::parse($d->tanggal_kirim)->format('d-m-Y') }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Custom dropdown with built-in filter --}}
                    <div class="custom-order-dropdown" id="customOrderDropdown">
                        <div class="custom-order-trigger form-select d-flex align-items-center" id="orderTrigger" style="cursor:pointer;">
                            <span id="orderTriggerText" class="text-muted">-- Pilih Order --</span>
                        </div>
                        <div class="custom-order-menu shadow" id="orderMenu" style="display:none; position:absolute; z-index:9999; background:#fff; border:1px solid #ced4da; border-radius:6px; width:100%; min-width:340px;">
                            {{-- Filter row inside dropdown --}}
                            <div class="p-2 border-bottom bg-light">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label mb-1" style="font-size:11px; color:#888;">Tanggal Kirim</label>
                                        <input type="date" id="inDropFilter_tanggal" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-1" style="font-size:11px; color:#888;">Toko</label>
                                        <select id="inDropFilter_toko" class="form-select form-select-sm">
                                            <option value="">Semua Toko</option>
                                            @php
                                                $uniqueTokos = collect($distribusis)->pluck('pesanan.toko')->unique('id')->sortBy('nama_toko');
                                            @endphp
                                            @foreach($uniqueTokos as $t)
                                                @if($t)
                                                    <option value="{{ $t->id }}">{{ $t->nama_toko }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- Search text --}}
                            <div class="px-2 pt-2">
                                <input type="text" id="inDropFilter_search" class="form-control form-control-sm" placeholder="Cari order...">
                            </div>
                            {{-- Items list --}}
                            <ul class="list-unstyled mb-0 mt-1" id="orderMenuList" style="max-height:220px; overflow-y:auto; padding: 4px 0;"></ul>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label">Kurir Pickup</label>
                    <select name="kurir_id" class="form-select" required>
                        <option value="">-- Pilih Kurir --</option>
                        @foreach ($kurirs as $kurir)
                            <option value="{{ $kurir->id }}">{{ $kurir->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Metode Refund</label>
                    <select name="refund_method" id="refund_method" class="form-select @error('refund_method') is-invalid @enderror" required>
                        <option value="uang_tunai" {{ old('refund_method') === 'uang_tunai' ? 'selected' : '' }}>Uang Tunai</option>
                        <option value="transfer" {{ old('refund_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="potong_piutang" {{ old('refund_method') === 'potong_piutang' ? 'selected' : '' }}>Potong Piutang</option>
                    </select>
                    <div class="invalid-feedback">
                        @error('refund_method') {{ $message }} @enderror
                    </div>
                    <small class="text-muted d-none" id="potong-piutang-note">Potong piutang hanya tersedia jika piutang order sudah lunas.</small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Retur</label>
                    <input type="date" name="tanggal_retur" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Jadwal Pengambilan</label>
                    <input type="date" name="tanggal_pengambilan" class="form-control" value="{{ now()->toDateString() }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Alasan</label>
                    <textarea name="alasan" class="form-control" rows="2" placeholder="Contoh: 10 pcs expired/rusak dari chat WhatsApp toko"></textarea>
                </div>

                <div class="col-md-12">
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Detail Retur</h6>
                        <button type="button" id="btn-tambah-retur" class="btn btn-sm btn-success">
                            <i class="bx bx-plus"></i> Tambah Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="tabel-retur">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th width="130">Qty</th>
                                    <th width="160">Kondisi</th>
                                    <th width="60">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="produk_id[]" class="form-select produk-select" required>
                                            <option value="">-- Pilih order dulu --</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <select name="kondisi[]" class="form-select" required>
                                            <option value="expired">Expired</option>
                                            <option value="rusak">Rusak</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus-retur">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <button class="btn btn-primary">Buat Tugas Retur</button>
                    <a href="{{ route('returs.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="template-retur-row">
    <tr>
        <td>
            <select name="produk_id[]" class="form-select produk-select" required>
                <option value="">-- Pilih order dulu --</option>
            </select>
        </td>
        <td>
            <input type="number" name="qty[]" class="form-control" min="1" value="1" required>
        </td>
        <td>
            <select name="kondisi[]" class="form-select" required>
                <option value="expired">Expired</option>
                <option value="rusak">Rusak</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger btn-hapus-retur">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var distribusiSelect = document.getElementById('distribusi_id');
    var refundMethod = document.getElementById('refund_method');
    var potongPiutangNote = document.getElementById('potong-piutang-note');
    var tabelRetur = document.getElementById('tabel-retur');
    var btnTambah = document.getElementById('btn-tambah-retur');

    function selectedItems() {
        var option = distribusiSelect.options[distribusiSelect.selectedIndex];
        if (!option || !option.dataset.items) {
            return [];
        }

        return JSON.parse(option.dataset.items);
    }

    function selectedDistribusiOption() {
        return distribusiSelect.options[distribusiSelect.selectedIndex];
    }

    function refreshRefundOptions() {
        if (!refundMethod) {
            return;
        }

        var option = selectedDistribusiOption();
        var potongOption = refundMethod.querySelector('option[value="potong_piutang"]');
        var canPotongPiutang = option && option.dataset.canPotongPiutang === '1';

        if (potongOption) {
            potongOption.disabled = !canPotongPiutang;
        }

        if (!canPotongPiutang && refundMethod.value === 'potong_piutang') {
            refundMethod.value = 'uang_tunai';
        }

        if (potongPiutangNote) {
            potongPiutangNote.classList.toggle('d-none', canPotongPiutang || !distribusiSelect.value);
        }
    }

    function refreshProdukOptions() {
        var items = selectedItems();
        document.querySelectorAll('.produk-select').forEach(function (select) {
            var currentValue = select.value;
            select.innerHTML = '<option value="">-- Pilih Produk --</option>';

            items.forEach(function (item) {
                var option = document.createElement('option');
                option.value = item.produk_id;
                option.textContent = item.nama_produk + ' | order ' + item.qty + ' pcs | Rp ' + Number(item.harga).toLocaleString('id-ID');
                option.dataset.maxQty = item.qty;
                select.appendChild(option);
            });

            select.value = currentValue;
        });
    }

    distribusiSelect.addEventListener('change', function () {
        refreshProdukOptions();
        refreshRefundOptions();
    });

    btnTambah.addEventListener('click', function () {
        var template = document.getElementById('template-retur-row');
        tabelRetur.querySelector('tbody').appendChild(template.content.cloneNode(true));
        refreshProdukOptions();
    });

    tabelRetur.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-hapus-retur');
        if (!btn) {
            return;
        }

        if (tabelRetur.querySelectorAll('tbody tr').length <= 1) {
            alert('Minimal harus ada 1 item retur.');
            return;
        }

        btn.closest('tr').remove();
    });

    tabelRetur.addEventListener('change', function (e) {
        if (!e.target.classList.contains('produk-select')) {
            return;
        }

        var selectedOption = e.target.options[e.target.selectedIndex];
        var qtyInput = e.target.closest('tr').querySelector('input[name="qty[]"]');
        if (selectedOption && selectedOption.dataset.maxQty) {
            qtyInput.max = selectedOption.dataset.maxQty;
        }
    });

    refreshProdukOptions();
    refreshRefundOptions();
});
</script>

@php
    $uniqueTokos = collect($distribusis)->pluck('pesanan.toko')->unique('id')->sortBy('nama_toko');
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Custom Order Dropdown Logic ──────────────────────────────
    var hiddenSelect   = document.getElementById('distribusi_id');
    var trigger        = document.getElementById('orderTrigger');
    var triggerText    = document.getElementById('orderTriggerText');
    var menu           = document.getElementById('orderMenu');
    var menuList       = document.getElementById('orderMenuList');
    var filterTanggal  = document.getElementById('inDropFilter_tanggal');
    var filterToko     = document.getElementById('inDropFilter_toko');
    var filterSearch   = document.getElementById('inDropFilter_search');

    // Build options data from hidden select
    var allOpts = Array.from(hiddenSelect.options).slice(1).map(function(opt) {
        return {
            value:              opt.value,
            text:               opt.textContent.trim(),
            tanggal:            opt.getAttribute('data-tanggal') || '',
            tokoId:             opt.getAttribute('data-toko-id') || '',
            items:              opt.getAttribute('data-items') || '[]',
            canPotong:          opt.getAttribute('data-can-potong-piutang') || '0',
            piutangStatus:      opt.getAttribute('data-piutang-status') || 'tidak_ada',
        };
    });

    function renderList() {
        var tgl    = filterTanggal.value;
        var toko   = filterToko.value;
        var search = filterSearch.value.toLowerCase();

        var filtered = allOpts.filter(function(o) {
            var matchTgl   = !tgl    || o.tanggal === tgl;
            var matchToko  = !toko   || o.tokoId  === toko;
            var matchSearch = !search || o.text.toLowerCase().indexOf(search) > -1;
            return matchTgl && matchToko && matchSearch;
        });

        menuList.innerHTML = '';

        if (filtered.length === 0) {
            menuList.innerHTML = '<li class="px-3 py-2 text-muted" style="font-size:13px;">Tidak ada order yang cocok</li>';
            return;
        }

        filtered.forEach(function(o) {
            var li = document.createElement('li');
            li.style.cssText = 'padding:7px 14px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0f0f0;';
            li.textContent = o.text;
            li.addEventListener('mouseenter', function() { li.style.background = '#f5f7ff'; });
            li.addEventListener('mouseleave', function() { li.style.background = ''; });
            li.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectOrder(o);
                closeMenu();
            });
            menuList.appendChild(li);
        });
    }

    function selectOrder(o) {
        // Update hidden select
        hiddenSelect.value = o.value;

        // Update trigger text
        triggerText.textContent = o.text;
        triggerText.classList.remove('text-muted');

        // Dispatch change event so existing JS listeners fire
        hiddenSelect.dispatchEvent(new Event('change'));
    }

    function openMenu() {
        menu.style.display = 'block';
        filterSearch.focus();
        renderList();
    }

    function closeMenu() {
        menu.style.display = 'none';
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        if (menu.style.display === 'none') {
            openMenu();
        } else {
            closeMenu();
        }
    });

    // Keep menu open when interacting inside
    menu.addEventListener('mousedown', function(e) { e.stopPropagation(); });
    menu.addEventListener('click', function(e) { e.stopPropagation(); });

    // Prevent date picker from closing menu
    // When date input is focused, set a flag so outside click doesn't close menu
    var datePickerActive = false;
    filterTanggal.addEventListener('focus', function() { datePickerActive = true; });
    filterTanggal.addEventListener('blur', function() {
        // Small delay to allow date selection to complete first
        setTimeout(function() { datePickerActive = false; }, 300);
    });
    // Also stop propagation on any click on the date input itself
    filterTanggal.addEventListener('click', function(e) { e.stopPropagation(); });

    // Close on outside click — but only if date picker is not active
    document.addEventListener('click', function() {
        if (!datePickerActive) {
            closeMenu();
        }
    });

    filterTanggal.addEventListener('change', renderList);
    filterToko.addEventListener('change', renderList);
    filterSearch.addEventListener('input', renderList);

    // Wrap dropdown in relative container
    var dropdown = document.getElementById('customOrderDropdown');
    dropdown.style.position = 'relative';
});
</script>
@endsection

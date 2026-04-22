@extends('layouts.app')

@section('content')
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
                    <select name="distribusi_id" id="distribusi_id" class="form-select" required>
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
                            @endphp
                            <option value="{{ $d->id }}" data-items='@json($items)'>
                                {{ $d->pesanan->order_code ?? '#' . $d->pesanan->id }} | {{ $d->pesanan->toko->nama_toko }} | {{ $d->tanggal_kirim }}
                            </option>
                        @endforeach
                    </select>
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
                    <select name="refund_method" class="form-select" required>
                        <option value="uang_tunai">Uang Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="potong_piutang">Potong Piutang</option>
                    </select>
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
    var tabelRetur = document.getElementById('tabel-retur');
    var btnTambah = document.getElementById('btn-tambah-retur');

    function selectedItems() {
        var option = distribusiSelect.options[distribusiSelect.selectedIndex];
        if (!option || !option.dataset.items) {
            return [];
        }

        return JSON.parse(option.dataset.items);
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

    distribusiSelect.addEventListener('change', refreshProdukOptions);

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
});
</script>
@endsection

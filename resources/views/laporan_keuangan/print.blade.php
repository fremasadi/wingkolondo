@php
    $formatCurrency = fn ($value) => 'Rp' . number_format((float) $value, 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 14mm;
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            color: #111827;
            background: #fff;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .toolbar h1 {
            font-size: 20px;
            margin: 0;
        }

        .toolbar p {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 13px;
        }

        .toolbar button,
        .toolbar a {
            border: 1px solid #111827;
            background: #fff;
            padding: 10px 14px;
            font-size: 13px;
            text-decoration: none;
            color: #111827;
            cursor: pointer;
        }

        .report-title {
            text-align: center;
            font-weight: 700;
            font-size: 24px;
            margin: 12px 0 18px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            border: 2px solid #111827;
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 14px;
        }

        thead th {
            background: #e89a9a;
            text-align: center;
            font-size: 18px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: 700;
        }

        .footer-label {
            text-align: center;
            font-weight: 700;
        }

        .negative {
            color: #b91c1c;
        }

        .positive {
            color: #166534;
        }

        @media print {
            .toolbar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>Laporan Keuangan Detail</h1>
            <p>Data diambil otomatis dari omzet penjualan cash/transfer dan piutang tempo yang sudah lunas.</p>
        </div>

        <div>
            <button type="button" onclick="window.print()">Print / Save PDF</button>
            <a href="{{ route('laporan-keuangan.index', ['bulan' => $selectedMonth, 'tahun' => $selectedYear, 'sumber' => $selectedSumber]) }}">Kembali</a>
        </div>
    </div>

    <div class="report-title">{{ $reportTitle }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 14%;">Referensi</th>
                <th style="width: 20%;">Toko</th>
                <th style="width: 18%;">Jenis Transaksi</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 12%;">Bruto</th>
                <th style="width: 12%;">Retur</th>
                <th style="width: 12%;">Netto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->tanggal->translatedFormat('d F Y') }}</td>
                    <td>{{ $entry->referensi }}</td>
                    <td>{{ $entry->pihak }}</td>
                    <td>{{ $entry->jenis_transaksi }}</td>
                    <td class="text-center">{{ $entry->metode_pembayaran }}</td>
                    <td class="text-end">{{ $formatCurrency($entry->bruto) }}</td>
                    <td class="text-end">{{ $entry->pengurang > 0 ? $formatCurrency($entry->pengurang) : '' }}</td>
                    <td class="text-end">{{ $formatCurrency($entry->netto) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada data laporan keuangan pada periode ini.</td>
                </tr>
            @endforelse

            <tr>
                <td colspan="5" class="footer-label">Total Cash/Transfer Bruto + Tempo Lunas</td>
                <td class="text-end fw-bold">{{ $formatCurrency($cashBruto + $tempoNet) }}</td>
                <td class="text-end"></td>
                <td class="text-end"></td>
            </tr>
            <tr>
                <td colspan="6" class="footer-label">Total Pengurang Retur Cash/Transfer</td>
                <td class="text-end fw-bold">{{ $formatCurrency($cashRetur) }}</td>
                <td class="text-end"></td>
            </tr>
            <tr>
                <td colspan="7" class="footer-label">Total Omzet Periode</td>
                <td class="text-end fw-bold">{{ $formatCurrency($totalOmzet) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

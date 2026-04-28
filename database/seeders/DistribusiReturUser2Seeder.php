<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\DetailRetur;
use App\Models\Distribusi;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Retur;
use App\Models\Toko;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DistribusiReturUser2Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $kurir = User::find(2);

            if (! $kurir) {
                throw new RuntimeException('User dengan ID 2 tidak ditemukan. Seeder ini butuh user_id 2 sebagai kurir.');
            }

            if ($kurir->role !== 'kurir') {
                $kurir->update(['role' => 'kurir']);
            }

            Pesanan::where('order_code', 'like', 'WL-U2TEST-%')->get()->each->delete();

            $tokoA = Toko::updateOrCreate(
                ['nama_toko' => 'Toko Testing Kurir 2 A'],
                [
                    'alamat' => 'Jl. Mawar No. 2, Kediri',
                    'no_hp' => '081200000201',
                    'latitude' => -7.81610000,
                    'longitude' => 112.01110000,
                ]
            );

            $tokoB = Toko::updateOrCreate(
                ['nama_toko' => 'Toko Testing Kurir 2 B'],
                [
                    'alamat' => 'Jl. Melati No. 12, Kediri',
                    'no_hp' => '081200000202',
                    'latitude' => -7.81820000,
                    'longitude' => 112.01320000,
                ]
            );

            $produkCoklat = Produk::updateOrCreate(
                ['nama_produk' => 'Roti Coklat Test U2'],
                ['stok' => 500, 'harga' => 12000]
            );

            $produkKeju = Produk::updateOrCreate(
                ['nama_produk' => 'Roti Keju Test U2'],
                ['stok' => 500, 'harga' => 15000]
            );

            $produkPisang = Produk::updateOrCreate(
                ['nama_produk' => 'Roti Pisang Test U2'],
                ['stok' => 500, 'harga' => 10000]
            );

            $tokos = [$tokoA, $tokoB];
            $produks = [$produkCoklat, $produkKeju, $produkPisang];

            foreach (range(1, 10) as $index) {
                $this->seedDistribusi(
                    orderCode: $this->makeOrderCode($index),
                    toko: $tokos[$index % count($tokos)],
                    kurirId: $kurir->id,
                    tanggalPesanan: Carbon::today()->subDays(10 + $index),
                    tanggalKirim: Carbon::today()->subDays($index % 3),
                    statusPesanan: 'diproses',
                    metodePembayaran: $this->pickPaymentMethod($index),
                    statusPengiriman: 'pending',
                    catatan: 'Data testing distribusi pending user 2 #' . $index,
                    approvedBy: null,
                    items: $this->makeDistribusiItems($produks, $index)
                );
            }

            foreach (range(11, 20) as $index) {
                $this->seedDistribusi(
                    orderCode: $this->makeOrderCode($index),
                    toko: $tokos[$index % count($tokos)],
                    kurirId: $kurir->id,
                    tanggalPesanan: Carbon::today()->subDays(10 + $index),
                    tanggalKirim: Carbon::today()->subDays(($index % 4) + 1),
                    statusPesanan: 'dikirim',
                    metodePembayaran: $this->pickPaymentMethod($index),
                    statusPengiriman: 'dikirim',
                    catatan: 'Data testing distribusi dikirim user 2 #' . $index,
                    approvedBy: null,
                    items: $this->makeDistribusiItems($produks, $index)
                );
            }

            foreach (range(21, 28) as $index) {
                $this->seedDistribusi(
                    orderCode: $this->makeOrderCode($index),
                    toko: $tokos[$index % count($tokos)],
                    kurirId: $kurir->id,
                    tanggalPesanan: Carbon::today()->subDays(10 + $index),
                    tanggalKirim: Carbon::today()->subDays(($index % 5) + 2),
                    statusPesanan: 'dikirim',
                    metodePembayaran: $this->pickPaymentMethod($index),
                    statusPengiriman: 'terkirim',
                    catatan: 'Data testing distribusi terkirim user 2 #' . $index,
                    approvedBy: null,
                    items: $this->makeDistribusiItems($produks, $index)
                );
            }

            foreach (range(29, 36) as $index) {
                $distribusi = $this->seedDistribusi(
                    orderCode: $this->makeOrderCode($index),
                    toko: $tokos[$index % count($tokos)],
                    kurirId: $kurir->id,
                    tanggalPesanan: Carbon::today()->subDays(10 + $index),
                    tanggalKirim: Carbon::today()->subDays(($index % 6) + 3),
                    statusPesanan: 'dikirim',
                    metodePembayaran: $this->pickPaymentMethod($index),
                    statusPengiriman: 'retur',
                    catatan: 'Data testing distribusi retur user 2 #' . $index,
                    approvedBy: null,
                    items: $this->makeDistribusiItems($produks, $index)
                );

                $this->seedRetur(
                    distribusi: $distribusi,
                    kurirId: $kurir->id,
                    adminId: null,
                    tanggalRetur: Carbon::today()->subDays(($index % 4) + 1),
                    tanggalPengambilan: Carbon::today()->subDays($index % 2),
                    status: $index % 2 === 0 ? 'dijemput' : 'ditugaskan',
                    alasan: $index % 2 === 0
                        ? 'Produk rusak saat diterima toko.'
                        : 'Produk mendekati expired.',
                    refundMethod: $this->pickRefundMethod($index),
                    pickupProof: $index % 2 === 0,
                    items: $this->makeReturItems($distribusi, $index)
                );
            }
        });
    }

    private function makeOrderCode(int $index): string
    {
        return sprintf('WL-U2TEST-%03d', $index);
    }

    private function pickPaymentMethod(int $index): string
    {
        $methods = ['cash', 'transfer', 'tempo'];

        return $methods[$index % count($methods)];
    }

    private function pickRefundMethod(int $index): string
    {
        $methods = ['uang_tunai', 'transfer', 'potong_piutang'];

        return $methods[$index % count($methods)];
    }

    private function makeDistribusiItems(array $produks, int $index): array
    {
        $firstProduk = $produks[$index % count($produks)];
        $secondProduk = $produks[($index + 1) % count($produks)];

        return [
            ['produk' => $firstProduk, 'qty' => ($index % 5) + 4],
            ['produk' => $secondProduk, 'qty' => ($index % 4) + 3],
        ];
    }

    private function makeReturItems(Distribusi $distribusi, int $index): array
    {
        $distribusi->loadMissing('pesanan.details.produk');

        return $distribusi->pesanan->details
            ->take(2)
            ->values()
            ->map(function ($detail, $detailIndex) use ($index) {
                $qty = min($detail->qty, $detailIndex === 0 ? 2 : 1);
                $kondisis = ['expired', 'rusak', 'lainnya'];

                return [
                    'produk' => $detail->produk,
                    'qty' => max(1, $qty),
                    'kondisi' => $kondisis[($index + $detailIndex) % count($kondisis)],
                ];
            })
            ->all();
    }

    private function seedDistribusi(
        string $orderCode,
        Toko $toko,
        int $kurirId,
        Carbon $tanggalPesanan,
        Carbon $tanggalKirim,
        string $statusPesanan,
        string $metodePembayaran,
        string $statusPengiriman,
        string $catatan,
        ?int $approvedBy,
        array $items
    ): Distribusi {
        $pesanan = Pesanan::updateOrCreate(
            ['order_code' => $orderCode],
            [
                'toko_id' => $toko->id,
                'tanggal_pesanan' => $tanggalPesanan->toDateString(),
                'tanggal_kirim' => $tanggalKirim->toDateString(),
                'status_pesanan' => $statusPesanan,
                'metode_pembayaran' => $metodePembayaran,
                'total_harga' => 0,
            ]
        );

        $produkIds = [];

        foreach ($items as $item) {
            $produkIds[] = $item['produk']->id;

            DetailPesanan::updateOrCreate(
                [
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $item['produk']->id,
                ],
                [
                    'qty' => $item['qty'],
                    'harga' => $item['produk']->harga,
                    'subtotal' => $item['qty'] * $item['produk']->harga,
                ]
            );
        }

        $pesanan->details()->whereNotIn('produk_id', $produkIds)->delete();
        $pesanan->updateTotalHarga();

        return Distribusi::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'kurir_id' => $kurirId,
                'tanggal_kirim' => $tanggalKirim->toDateString(),
                'status_pengiriman' => $statusPengiriman,
                'catatan' => $catatan,
                'delivered_at' => in_array($statusPengiriman, ['terkirim', 'retur', 'selesai'], true)
                    ? $tanggalKirim->copy()->setTime(14, 0)
                    : null,
                'delivery_latitude' => in_array($statusPengiriman, ['terkirim', 'retur', 'selesai'], true)
                    ? $toko->latitude
                    : null,
                'delivery_longitude' => in_array($statusPengiriman, ['terkirim', 'retur', 'selesai'], true)
                    ? $toko->longitude
                    : null,
                'delivery_photo' => null,
                'delivery_note' => in_array($statusPengiriman, ['terkirim', 'retur', 'selesai'], true)
                    ? 'Bukti kirim dummy untuk testing user 2.'
                    : null,
                'approved_by' => $statusPengiriman === 'selesai' ? $approvedBy : null,
                'approved_at' => $statusPengiriman === 'selesai' && $approvedBy
                    ? $tanggalKirim->copy()->setTime(16, 0)
                    : null,
            ]
        );
    }

    private function seedRetur(
        Distribusi $distribusi,
        int $kurirId,
        ?int $adminId,
        Carbon $tanggalRetur,
        Carbon $tanggalPengambilan,
        string $status,
        string $alasan,
        string $refundMethod,
        bool $pickupProof,
        array $items
    ): void {
        $retur = Retur::updateOrCreate(
            ['distribusi_id' => $distribusi->id],
            [
                'kurir_id' => $kurirId,
                'tanggal_retur' => $tanggalRetur->toDateString(),
                'status' => $status,
                'alasan' => $alasan,
                'refund_method' => $refundMethod,
                'total_retur' => 0,
                'total_refund' => 0,
                'tanggal_pengambilan' => $tanggalPengambilan->toDateString(),
                'picked_up_at' => $pickupProof ? $tanggalPengambilan->copy()->setTime(10, 0) : null,
                'pickup_latitude' => $pickupProof ? $distribusi->pesanan->toko->latitude : null,
                'pickup_longitude' => $pickupProof ? $distribusi->pesanan->toko->longitude : null,
                'pickup_photo' => null,
                'pickup_note' => $pickupProof ? 'Bukti pickup dummy untuk testing user 2.' : null,
                'approved_by' => $status === 'selesai' ? $adminId : null,
                'approved_at' => $status === 'selesai' && $adminId
                    ? $tanggalPengambilan->copy()->setTime(15, 0)
                    : null,
            ]
        );

        $produkIds = [];
        $total = 0;

        foreach ($items as $item) {
            $produkIds[] = $item['produk']->id;
            $subtotal = $item['qty'] * $item['produk']->harga;

            DetailRetur::updateOrCreate(
                [
                    'retur_id' => $retur->id,
                    'produk_id' => $item['produk']->id,
                ],
                [
                    'qty' => $item['qty'],
                    'kondisi' => $item['kondisi'],
                    'harga' => $item['produk']->harga,
                    'subtotal' => $subtotal,
                ]
            );

            $total += $subtotal;
        }

        $retur->details()->whereNotIn('produk_id', $produkIds)->delete();

        $retur->update([
            'total_retur' => $total,
            'total_refund' => $total,
        ]);
    }
}

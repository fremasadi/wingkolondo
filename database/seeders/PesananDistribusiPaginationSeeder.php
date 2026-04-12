<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Distribusi;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PesananDistribusiPaginationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $kurir = User::updateOrCreate(
                ['id' => 1],
                [
                    'name' => 'Kurir Pagination',
                    'email' => 'kurir-pagination@example.com',
                    'email_verified_at' => now(),
                    'no_hp' => '081111111111',
                    'role' => 'kurir',
                    'password' => Hash::make('password123'),
                ]
            );

            $admin = User::firstOrCreate(
                ['email' => 'admin-pagination@example.com'],
                [
                    'name' => 'Admin Pagination',
                    'email_verified_at' => now(),
                    'no_hp' => '082222222222',
                    'role' => 'admin',
                    'password' => Hash::make('password123'),
                ]
            );

            $otherKurir = User::firstOrCreate(
                ['email' => 'kurir-lain@example.com'],
                [
                    'name' => 'Kurir Lain',
                    'email_verified_at' => now(),
                    'no_hp' => '083333333333',
                    'role' => 'kurir',
                    'password' => Hash::make('password123'),
                ]
            );

            $tokos = collect(range(1, 6))->map(function ($index) {
                return Toko::firstOrCreate(
                    ['nama_toko' => 'Toko Pagination ' . $index],
                    [
                        'alamat' => 'Jl. Contoh Pagination No. ' . $index . ', Kediri',
                        'no_hp' => '0857000000' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                        'latitude' => -7.816895 + ($index * 0.001),
                        'longitude' => 112.011398 + ($index * 0.001),
                    ]
                );
            });

            $produks = collect([
                ['nama_produk' => 'Roti Coklat Pagination', 'stok' => 500, 'harga' => 12000],
                ['nama_produk' => 'Roti Keju Pagination', 'stok' => 500, 'harga' => 15000],
                ['nama_produk' => 'Roti Pisang Pagination', 'stok' => 500, 'harga' => 10000],
            ])->map(function (array $produk) {
                return Produk::firstOrCreate(
                    ['nama_produk' => $produk['nama_produk']],
                    [
                        'stok' => $produk['stok'],
                        'harga' => $produk['harga'],
                    ]
                );
            })->values();

            foreach (range(1, 30) as $index) {
                $this->seedPesananDistribusi(
                    toko: $tokos[($index - 1) % $tokos->count()],
                    produk: $produks[($index - 1) % $produks->count()],
                    kurirId: $kurir->id,
                    tanggalPesanan: Carbon::today()->subDays($index),
                    tanggalKirim: Carbon::today(),
                    statusPengiriman: $index <= 10 ? 'pending' : ($index <= 20 ? 'dikirim' : 'terkirim'),
                    catatan: 'Seeder pagination kurir 1 hari ini #' . $index,
                    approvedBy: $index > 20 ? $admin->id : null
                );
            }

            foreach (range(1, 12) as $index) {
                $this->seedPesananDistribusi(
                    toko: $tokos[($index - 1) % $tokos->count()],
                    produk: $produks[($index - 1) % $produks->count()],
                    kurirId: $kurir->id,
                    tanggalPesanan: Carbon::today()->subDays(40 + $index),
                    tanggalKirim: Carbon::today()->subDays(($index % 4) + 1),
                    statusPengiriman: $index % 3 === 0 ? 'terkirim' : 'dikirim',
                    catatan: 'Seeder pagination kurir 1 histori #' . $index,
                    approvedBy: $index % 3 === 0 ? $admin->id : null
                );
            }

            foreach (range(1, 8) as $index) {
                $this->seedPesananDistribusi(
                    toko: $tokos[($index - 1) % $tokos->count()],
                    produk: $produks[($index - 1) % $produks->count()],
                    kurirId: $otherKurir->id,
                    tanggalPesanan: Carbon::today()->subDays(15 + $index),
                    tanggalKirim: Carbon::today(),
                    statusPengiriman: 'dikirim',
                    catatan: 'Seeder pagination kurir lain #' . $index,
                    approvedBy: null
                );
            }
        });
    }

    private function seedPesananDistribusi(
        Toko $toko,
        Produk $produk,
        int $kurirId,
        Carbon $tanggalPesanan,
        Carbon $tanggalKirim,
        string $statusPengiriman,
        string $catatan,
        ?int $approvedBy
    ): void {
        $pesanan = Pesanan::firstOrCreate(
            [
                'toko_id' => $toko->id,
                'tanggal_pesanan' => $tanggalPesanan->toDateString(),
                'tanggal_kirim' => $tanggalKirim->toDateString(),
            ],
            [
                'status_pesanan' => $statusPengiriman === 'pending' ? 'diproses' : 'dikirim',
                'metode_pembayaran' => 'cash',
                'total_harga' => 0,
            ]
        );

        if (! $pesanan->details()->exists()) {
            $qty = (($pesanan->id % 4) + 1) * 2;

            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produk->id,
                'qty' => $qty,
                'harga' => $produk->harga,
                'subtotal' => $qty * $produk->harga,
            ]);

            $pesanan->updateTotalHarga();
        }

        Distribusi::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'kurir_id' => $kurirId,
                'tanggal_kirim' => $tanggalKirim->toDateString(),
                'status_pengiriman' => $statusPengiriman,
                'catatan' => $catatan,
                'delivered_at' => $statusPengiriman === 'terkirim' ? $tanggalKirim->copy()->setTime(14, 0) : null,
                'delivery_latitude' => $statusPengiriman === 'terkirim' ? $toko->latitude : null,
                'delivery_longitude' => $statusPengiriman === 'terkirim' ? $toko->longitude : null,
                'delivery_note' => $statusPengiriman === 'terkirim' ? 'Paket diterima toko untuk test pagination.' : null,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedBy ? $tanggalKirim->copy()->setTime(16, 0) : null,
            ]
        );
    }
}

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

class TestingPesananDistribusiUser2Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $kurir = User::find(2);

            if (! $kurir) {
                $kurir = User::factory()->create([
                    'id' => 2,
                    'name' => 'Kurir Testing User 2',
                    'email' => 'kurir-user-2-testing@example.com',
                    'no_hp' => '082345678901',
                    'role' => 'kurir',
                ]);
            }

            if ($kurir->role !== 'kurir') {
                $kurir->update(['role' => 'kurir']);
            }

            Pesanan::where('order_code', 'like', 'WL-U2DIST-%')->get()->each->delete();

            $tokos = collect([
                [
                    'nama_toko' => 'Toko U2 Distribusi A',
                    'alamat' => 'Jl. Panglima Sudirman No. 12, Kediri',
                    'no_hp' => '081200002001',
                    'latitude' => -7.816895,
                    'longitude' => 112.011398,
                ],
                [
                    'nama_toko' => 'Toko U2 Distribusi B',
                    'alamat' => 'Jl. Hayam Wuruk No. 18, Kediri',
                    'no_hp' => '081200002002',
                    'latitude' => -7.819120,
                    'longitude' => 112.014280,
                ],
                [
                    'nama_toko' => 'Toko U2 Distribusi C',
                    'alamat' => 'Jl. Dhoho No. 27, Kediri',
                    'no_hp' => '081200002003',
                    'latitude' => -7.812520,
                    'longitude' => 112.017450,
                ],
            ])->map(fn (array $toko) => Toko::updateOrCreate(
                ['nama_toko' => $toko['nama_toko']],
                $toko
            ))->values();

            $produks = collect([
                ['nama_produk' => 'Wingko Original Test U2', 'stok' => 500, 'harga' => 10000],
                ['nama_produk' => 'Wingko Coklat Test U2', 'stok' => 500, 'harga' => 12000],
                ['nama_produk' => 'Wingko Keju Test U2', 'stok' => 500, 'harga' => 14000],
            ])->map(fn (array $produk) => Produk::updateOrCreate(
                ['nama_produk' => $produk['nama_produk']],
                $produk
            ))->values();

            foreach (range(1, 16) as $index) {
                $statusPengiriman = $this->statusPengiriman($index);
                $tanggalKirim = Carbon::today()->addDays($index <= 4 ? 0 : -($index % 7));

                $this->seedPesananDistribusi(
                    orderCode: sprintf('WL-U2DIST-%03d', $index),
                    toko: $tokos[($index - 1) % $tokos->count()],
                    produks: $produks,
                    kurirId: $kurir->id,
                    tanggalPesanan: $tanggalKirim->copy()->subDays(($index % 4) + 1),
                    tanggalKirim: $tanggalKirim,
                    statusPengiriman: $statusPengiriman,
                    metodePembayaran: $this->metodePembayaran($index),
                    catatan: 'Data testing pesanan distribusi user 2 #' . $index
                );
            }
        });
    }

    private function seedPesananDistribusi(
        string $orderCode,
        Toko $toko,
        mixed $produks,
        int $kurirId,
        Carbon $tanggalPesanan,
        Carbon $tanggalKirim,
        string $statusPengiriman,
        string $metodePembayaran,
        string $catatan
    ): void {
        $pesanan = Pesanan::updateOrCreate(
            ['order_code' => $orderCode],
            [
                'toko_id' => $toko->id,
                'tanggal_pesanan' => $tanggalPesanan->toDateString(),
                'tanggal_kirim' => $tanggalKirim->toDateString(),
                'status_pesanan' => $this->statusPesanan($statusPengiriman),
                'metode_pembayaran' => $metodePembayaran,
                'total_harga' => 0,
            ]
        );

        $produkIds = [];

        foreach ([0, 1] as $itemIndex) {
            $produk = $produks[($pesanan->id + $itemIndex) % $produks->count()];
            $qty = (($pesanan->id + $itemIndex) % 5) + 2;
            $produkIds[] = $produk->id;

            DetailPesanan::updateOrCreate(
                [
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $produk->id,
                ],
                [
                    'qty' => $qty,
                    'harga' => $produk->harga,
                    'subtotal' => $qty * $produk->harga,
                ]
            );
        }

        $pesanan->details()->whereNotIn('produk_id', $produkIds)->delete();
        $pesanan->updateTotalHarga();

        Distribusi::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'kurir_id' => $kurirId,
                'tanggal_kirim' => $tanggalKirim->toDateString(),
                'status_pengiriman' => $statusPengiriman,
                'catatan' => $catatan,
                'delivered_at' => in_array($statusPengiriman, ['terkirim', 'selesai'], true)
                    ? $tanggalKirim->copy()->setTime(14, 30)
                    : null,
                'delivery_latitude' => in_array($statusPengiriman, ['terkirim', 'selesai'], true)
                    ? $toko->latitude
                    : null,
                'delivery_longitude' => in_array($statusPengiriman, ['terkirim', 'selesai'], true)
                    ? $toko->longitude
                    : null,
                'delivery_photo' => null,
                'delivery_note' => in_array($statusPengiriman, ['terkirim', 'selesai'], true)
                    ? 'Bukti pengiriman dummy untuk testing user 2.'
                    : null,
                'approved_by' => null,
                'approved_at' => $statusPengiriman === 'selesai'
                    ? $tanggalKirim->copy()->setTime(16, 0)
                    : null,
            ]
        );
    }

    private function statusPengiriman(int $index): string
    {
        return match (true) {
            $index <= 4 => 'pending',
            $index <= 8 => 'dikirim',
            $index <= 12 => 'terkirim',
            default => 'selesai',
        };
    }

    private function statusPesanan(string $statusPengiriman): string
    {
        return match ($statusPengiriman) {
            'pending' => 'diproses',
            'selesai' => 'selesai',
            default => 'dikirim',
        };
    }

    private function metodePembayaran(int $index): string
    {
        return ['cash', 'transfer', 'tempo'][$index % 3];
    }
}

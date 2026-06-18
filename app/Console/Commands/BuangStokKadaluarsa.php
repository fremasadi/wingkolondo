<?php

namespace App\Console\Commands;

use App\Models\Produk;
use App\Models\PembuanganStok;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuangStokKadaluarsa extends Command
{
    protected $signature   = 'stok:buang-kadaluarsa';
    protected $description = 'Otomatis membuang stok produk yang sudah kadaluarsa (30 hari sejak produksi)';

    public function handle()
    {
        $this->info('Memeriksa stok kadaluarsa...');

        $produks = Produk::where('stok', '>', 0)->get();
        $count   = 0;
        $totalQty = 0;

        DB::transaction(function () use ($produks, &$count, &$totalQty) {
            foreach ($produks as $produk) {
                $exp = $produk->tanggal_kadaluarsa;

                if (!$exp || !$exp->isPast()) {
                    continue;
                }

                // Skip jika sudah dibuang otomatis hari ini
                $sudahDibuang = PembuanganStok::where('produk_id', $produk->id)
                    ->where('metode', 'otomatis')
                    ->whereDate('tanggal_buang', today())
                    ->exists();

                if ($sudahDibuang) {
                    continue;
                }

                $qtyBuang = $produk->stok;

                PembuanganStok::create([
                    'produk_id'     => $produk->id,
                    'qty'           => $qtyBuang,
                    'tanggal_buang' => today(),
                    'keterangan'    => 'Otomatis - Kadaluarsa ' . $exp->format('d/m/Y'),
                    'metode'        => 'otomatis',
                ]);

                $produk->decrement('stok', $qtyBuang);

                $this->line("  → [{$produk->nama_produk}] {$qtyBuang} pcs dibuang (kadaluarsa {$exp->format('d/m/Y')})");
                $count++;
                $totalQty += $qtyBuang;
            }
        });

        if ($count === 0) {
            $this->info('Tidak ada stok yang perlu dibuang hari ini.');
        } else {
            $this->info("Selesai: {$count} produk ({$totalQty} pcs) dibuang.");
        }

        return Command::SUCCESS;
    }
}

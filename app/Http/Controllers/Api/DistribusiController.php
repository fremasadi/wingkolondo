<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distribusi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DistribusiController extends Controller
{
    public function index(Request $request)
    {
        $kurir = $request->user();

        // keamanan: pastikan kurir
        if ($kurir->role !== 'kurir') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $startDate = Carbon::parse($request->query('start_date', Carbon::today()->toDateString()))->toDateString();
        $endDate = Carbon::parse($request->query('end_date', $startDate))->toDateString();
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        if ($status !== null && ! in_array($status, ['pending', 'dikirim', 'selesai'], true)) {
            return response()->json([
                'message' => 'Status distribusi tidak valid. Gunakan pending, dikirim, atau selesai.',
            ], 422);
        }

        $distribusis = Distribusi::with([
                'pesanan.toko',
                'pesanan.details.produk',
                'approver',
            ])
            ->where('kurir_id', $kurir->id)
            ->whereDate('tanggal_kirim', '>=', $startDate)
            ->whereDate('tanggal_kirim', '<=', $endDate)
            ->when($status, fn ($query) => $query->where('status_pengiriman', $status))
            ->orderBy('tanggal_kirim', 'desc')
            ->paginate($perPage)
            ->through(function ($d) {
                $toko = $d->pesanan->toko;

                return [
                    'id' => $d->id,
                    'tanggal_kirim' => $d->tanggal_kirim,
                    'status_pengiriman' => $d->status_pengiriman,
                    'catatan' => $d->catatan,
                    'toko' => [
                        'nama' => $toko->nama_toko,
                        'alamat' => $toko->alamat,
                        'no_hp' => $toko->no_hp,
                        'latitude' => $toko->latitude,
                        'longitude' => $toko->longitude,
                    ],
                    'pesanan' => [
                        'id' => $d->pesanan->id,
                        'order_code' => $d->pesanan->order_code,
                        'tanggal_pesanan' => $d->pesanan->tanggal_pesanan,
                        'total_harga' => $d->pesanan->total_harga,
                        'metode_pembayaran' => $d->pesanan->metode_pembayaran,
                    ],
                    'bukti_pengiriman' => [
                        'delivered_at' => optional($d->delivered_at)->toDateTimeString(),
                        'latitude' => $d->delivery_latitude,
                        'longitude' => $d->delivery_longitude,
                        'photo' => $d->delivery_photo,
                        'photo_url' => $d->delivery_photo ? asset('storage/' . $d->delivery_photo) : null,
                        'note' => $d->delivery_note,
                        'approved_at' => optional($d->approved_at)->toDateTimeString(),
                        'approved_by' => $d->approver?->name,
                    ],
                    'items' => $d->pesanan->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'produk_id' => $detail->produk_id,
                            'nama_produk' => $detail->produk->nama_produk,
                            'qty' => $detail->qty,
                            'harga' => $detail->harga,
                            'subtotal' => $detail->subtotal,
                        ];
                    }),
                ];
            });

        return response()->json([
            'message' => 'Daftar distribusi',
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'per_page' => $distribusis->perPage(),
            ],
            'pagination' => [
                'current_page' => $distribusis->currentPage(),
                'last_page' => $distribusis->lastPage(),
                'per_page' => $distribusis->perPage(),
                'total' => $distribusis->total(),
                'from' => $distribusis->firstItem(),
                'to' => $distribusis->lastItem(),
                'has_more_pages' => $distribusis->hasMorePages(),
            ],
            'data' => $distribusis->items(),
        ]);
    }

    public function confirmDelivered(Request $request, Distribusi $distribusi)
    {
        $kurir = $request->user();

        if ($kurir->role !== 'kurir') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        if ((int) $distribusi->kurir_id !== (int) $kurir->id) {
            return response()->json([
                'message' => 'Distribusi ini bukan tugas Anda'
            ], 403);
        }

        if (in_array($distribusi->status_pengiriman, ['selesai', 'retur'], true)) {
            return response()->json([
                'message' => 'Distribusi ini tidak bisa dikonfirmasi lagi'
            ], 422);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'catatan' => 'nullable|string',
        ]);

        if ($distribusi->delivery_photo) {
            Storage::disk('public')->delete($distribusi->delivery_photo);
        }

        $photoPath = $request->file('foto')->store('delivery-proofs', 'public');

        $distribusi->update([
            'status_pengiriman' => 'terkirim',
            'delivery_latitude' => $validated['latitude'],
            'delivery_longitude' => $validated['longitude'],
            'delivery_photo' => $photoPath,
            'delivery_note' => $validated['catatan'] ?? null,
            'delivered_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return response()->json([
            'message' => 'Bukti pengiriman berhasil dikirim. Menunggu persetujuan admin.',
            'data' => [
                'id' => $distribusi->id,
                'status_pengiriman' => $distribusi->status_pengiriman,
                'delivered_at' => optional($distribusi->delivered_at)->toDateTimeString(),
                'latitude' => $distribusi->delivery_latitude,
                'longitude' => $distribusi->delivery_longitude,
                'photo' => $distribusi->delivery_photo,
                'photo_url' => asset('storage/' . $distribusi->delivery_photo),
                'catatan' => $distribusi->delivery_note,
            ],
        ]);
    }
}

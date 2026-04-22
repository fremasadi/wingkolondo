<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Retur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReturController extends Controller
{
    public function index(Request $request)
    {
        $kurir = $request->user();

        if ($kurir->role !== 'kurir') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $startDate = Carbon::parse($request->query('start_date', Carbon::today()->toDateString()))->toDateString();
        $endDate = Carbon::parse($request->query('end_date', $startDate))->toDateString();
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $returs = Retur::with([
                'distribusi.pesanan.toko',
                'details.produk',
                'approver',
            ])
            ->where('kurir_id', $kurir->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_pengambilan', [$startDate, $endDate])
                    ->orWhere(function ($fallback) use ($startDate, $endDate) {
                        $fallback->whereNull('tanggal_pengambilan')
                            ->whereBetween('tanggal_retur', [$startDate, $endDate]);
                    });
            })
            ->orderByRaw('COALESCE(tanggal_pengambilan, tanggal_retur) desc')
            ->paginate($perPage)
            ->through(function ($retur) {
                $pesanan = $retur->distribusi->pesanan;
                $toko = $pesanan->toko;

                return [
                    'id' => $retur->id,
                    'status' => $retur->status,
                    'tanggal_retur' => optional($retur->tanggal_retur)->toDateString(),
                    'tanggal_pengambilan' => optional($retur->tanggal_pengambilan)->toDateString(),
                    'alasan' => $retur->alasan,
                    'refund_method' => $retur->refund_method,
                    'total_refund' => $retur->total_refund,
                    'toko' => [
                        'nama' => $toko->nama_toko,
                        'alamat' => $toko->alamat,
                        'no_hp' => $toko->no_hp,
                        'latitude' => $toko->latitude,
                        'longitude' => $toko->longitude,
                    ],
                    'pesanan' => [
                        'id' => $pesanan->id,
                        'order_code' => $pesanan->order_code,
                        'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                        'total_harga' => $pesanan->total_harga,
                    ],
                    'bukti_pickup' => [
                        'picked_up_at' => optional($retur->picked_up_at)->toDateTimeString(),
                        'latitude' => $retur->pickup_latitude,
                        'longitude' => $retur->pickup_longitude,
                        'photo' => $retur->pickup_photo,
                        'photo_url' => $retur->pickup_photo ? asset('storage/' . $retur->pickup_photo) : null,
                        'note' => $retur->pickup_note,
                        'approved_at' => optional($retur->approved_at)->toDateTimeString(),
                        'approved_by' => $retur->approver?->name,
                    ],
                    'items' => $retur->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'produk_id' => $detail->produk_id,
                            'nama_produk' => $detail->produk->nama_produk,
                            'qty' => $detail->qty,
                            'kondisi' => $detail->kondisi,
                            'harga' => $detail->harga,
                            'subtotal' => $detail->subtotal,
                        ];
                    }),
                ];
            });

        return response()->json([
            'message' => 'Daftar tugas retur',
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $returs->perPage(),
            ],
            'pagination' => [
                'current_page' => $returs->currentPage(),
                'last_page' => $returs->lastPage(),
                'per_page' => $returs->perPage(),
                'total' => $returs->total(),
                'from' => $returs->firstItem(),
                'to' => $returs->lastItem(),
                'has_more_pages' => $returs->hasMorePages(),
            ],
            'data' => $returs->items(),
        ]);
    }

    public function confirmPickup(Request $request, Retur $retur)
    {
        $kurir = $request->user();

        if ($kurir->role !== 'kurir') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        if ((int) $retur->kurir_id !== (int) $kurir->id) {
            return response()->json([
                'message' => 'Retur ini bukan tugas Anda'
            ], 403);
        }

        if (in_array($retur->status, ['selesai', 'batal'], true)) {
            return response()->json([
                'message' => 'Retur ini tidak bisa dikonfirmasi lagi'
            ], 422);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'catatan' => 'nullable|string',
        ]);

        if ($retur->pickup_photo) {
            Storage::disk('public')->delete($retur->pickup_photo);
        }

        $photoPath = $request->file('foto')->store('return-pickups', 'public');

        $retur->update([
            'status' => 'dijemput',
            'pickup_latitude' => $validated['latitude'],
            'pickup_longitude' => $validated['longitude'],
            'pickup_photo' => $photoPath,
            'pickup_note' => $validated['catatan'] ?? null,
            'picked_up_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return response()->json([
            'message' => 'Bukti pickup retur berhasil dikirim. Menunggu persetujuan admin.',
            'data' => [
                'id' => $retur->id,
                'status' => $retur->status,
                'picked_up_at' => optional($retur->picked_up_at)->toDateTimeString(),
                'latitude' => $retur->pickup_latitude,
                'longitude' => $retur->pickup_longitude,
                'photo' => $retur->pickup_photo,
                'photo_url' => asset('storage/' . $retur->pickup_photo),
                'catatan' => $retur->pickup_note,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Retur;
use App\Models\Distribusi;
use App\Models\DetailRetur;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ReturController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSelesai = $request->query('tanggal_selesai');

        $query = Retur::with(['distribusi.pesanan.toko', 'kurir', 'approver']);

        if ($tanggalMulai) {
            $query->whereDate('tanggal_retur', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $query->whereDate('tanggal_retur', '<=', $tanggalSelesai);
        }

        $returs = $query->latest()->get();

        return view('retur.index', compact('returs', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function create()
    {
        $distribusis = Distribusi::where('status_pengiriman', 'selesai')
            ->doesntHave('retur')
            ->with(['pesanan.toko', 'pesanan.details.produk', 'pesanan.piutang'])
            ->get();
        $kurirs = User::where('role', 'kurir')->orderBy('name')->get();

        return view('retur.create', compact('distribusis', 'kurirs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distribusi_id' => 'required|exists:distribusis,id',
            'kurir_id' => 'required|exists:users,id',
            'tanggal_retur' => 'required|date',
            'tanggal_pengambilan' => 'nullable|date|after_or_equal:tanggal_retur',
            'refund_method' => 'required|in:uang_tunai,transfer,potong_piutang',
            'alasan' => 'nullable|string',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|exists:produks,id',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
            'kondisi' => 'required|array|min:1',
            'kondisi.*' => 'required|in:expired,rusak,lainnya',
        ]);

        $this->validatePotongPiutang($request);

        DB::transaction(function () use ($request) {

            $distribusi = Distribusi::with(['pesanan.details', 'pesanan.piutang'])->findOrFail($request->distribusi_id);

            if ($distribusi->retur) {
                abort(400, 'Distribusi sudah diretur');
            }

            $retur = Retur::create([
                'distribusi_id' => $distribusi->id,
                'kurir_id' => $request->kurir_id,
                'tanggal_retur' => $request->tanggal_retur,
                'tanggal_pengambilan' => $request->tanggal_pengambilan,
                'status' => 'ditugaskan',
                'alasan' => $request->alasan,
                'refund_method' => $request->refund_method,
                'total_retur' => 0,
                'total_refund' => 0,
            ]);

            $total = 0;

            foreach ($request->produk_id as $i => $produkId) {
                $qty = $request->qty[$i];

                $detailPesanan = $distribusi->pesanan
                    ->details()
                    ->where('produk_id', $produkId)
                    ->firstOrFail();

                if ($qty > $detailPesanan->qty) {
                    abort(422, 'Qty retur tidak boleh melebihi qty pesanan.');
                }

                $subtotal = $qty * $detailPesanan->harga;

                DetailRetur::create([
                    'retur_id' => $retur->id,
                    'produk_id' => $produkId,
                    'qty' => $qty,
                    'kondisi' => $request->kondisi[$i],
                    'harga' => $detailPesanan->harga,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            $retur->update([
                'total_retur' => $total,
                'total_refund' => $total,
            ]);
        });

        return redirect()->route('returs.index')->with('success', 'Tugas retur berhasil dibuat');
    }

    public function show(Retur $retur)
    {
        $retur->load('details.produk', 'distribusi.pesanan.toko', 'kurir', 'approver');

        return view('retur.show', compact('retur'));
    }

    public function approve(Retur $retur)
    {
        if ($retur->status === 'selesai') {
            return back()->with('success', 'Retur sudah selesai');
        }

        if ($retur->status !== 'dijemput') {
            return back()->with('error', 'Retur belum dikonfirmasi pickup oleh kurir.');
        }

        $retur->complete(auth()->id());

        return redirect()->route('returs.show', $retur)->with('success', 'Retur selesai dan refund sudah diproses');
    }

    private function validatePotongPiutang(Request $request): void
    {
        if ($request->refund_method !== 'potong_piutang') {
            return;
        }

        $distribusi = Distribusi::with('pesanan.piutang')->findOrFail($request->distribusi_id);
        $piutang = $distribusi->pesanan?->piutang;

        if (! $piutang || $piutang->status !== 'lunas') {
            throw ValidationException::withMessages([
                'refund_method' => 'Metode potong piutang hanya bisa dipilih jika piutang order sudah lunas.',
            ]);
        }
    }
}

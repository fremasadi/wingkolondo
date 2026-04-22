<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use Illuminate\Http\Request;

class PiutangController extends Controller
{
    public function index()
    {
        $piutangs = Piutang::with('toko','pesanan')->latest()->get();
        return view('piutang.index', compact('piutangs'));
    }

    public function edit(Piutang $piutang)
    {
        return view('piutang.edit', compact('piutang'));
    }

    public function update(Request $request, Piutang $piutang)
    {
        $request->validate([
            'total_dibayar' => 'required|numeric|min:0|max:' . $piutang->total_tagihan
        ]);

        $sisaTagihan = $piutang->total_tagihan - $request->total_dibayar;

        $status = $sisaTagihan == 0
            ? 'lunas'
            : 'belum_lunas';

        $piutang->update([
            'sisa_tagihan' => $sisaTagihan,
            'status' => $status
        ]);

        return redirect()->route('piutangs.index')
            ->with('success', 'Piutang berhasil diperbarui');
    }
}

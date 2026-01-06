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
            'sisa_tagihan' => 'required|numeric|min:0'
        ]);

        $status = $request->sisa_tagihan == 0
            ? 'lunas'
            : 'belum_lunas';

        $piutang->update([
            'sisa_tagihan' => $request->sisa_tagihan,
            'status' => $status
        ]);

        return redirect()->route('piutangs.index')
            ->with('success', 'Piutang berhasil diperbarui');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index()
    {
        $bahanBakus = BahanBaku::latest()->get();
        return view('bahan_baku.index', compact('bahanBakus'));
    }

    public function create()
    {
        return view('bahan_baku.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required'
        ]);

        BahanBaku::create($request->all());

        return redirect()->route('bahan-bakus.index')
            ->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function edit(BahanBaku $bahanBaku)
    {
        return view('bahan_baku.edit', compact('bahanBaku'));
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required'
        ]);

        $bahanBaku->update($request->all());

        return redirect()->route('bahan-bakus.index')
            ->with('success', 'Bahan baku berhasil diperbarui');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();

        return redirect()->route('bahan-bakus.index')
            ->with('success', 'Bahan baku berhasil dihapus');
    }
}
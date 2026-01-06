<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::latest()->get();
        return view('toko.index', compact('tokos'));
    }

    public function create()
    {
        return view('toko.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required',
            'alamat' => 'required',
            'no_hp' => 'nullable'
        ]);

        Toko::create($request->all());

        return redirect()->route('tokos.index')
            ->with('success', 'Toko berhasil ditambahkan');
    }

    public function edit(Toko $toko)
    {
        return view('toko.edit', compact('toko'));
    }

    public function update(Request $request, Toko $toko)
    {
        $request->validate([
            'nama_toko' => 'required',
            'alamat' => 'required',
            'no_hp' => 'nullable'
        ]);

        $toko->update($request->all());

        return redirect()->route('tokos.index')
            ->with('success', 'Toko berhasil diperbarui');
    }

    public function destroy(Toko $toko)
    {
        $toko->delete();

        return redirect()->route('tokos.index')
            ->with('success', 'Toko berhasil dihapus');
    }
}
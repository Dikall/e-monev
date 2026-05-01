<?php

namespace App\Http\Controllers;

use App\Models\PublicBody;
use App\Models\Kategori;
use Illuminate\Http\Request;

class PublicBodyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunSekarang = now()->year;

        $publicBodies = PublicBody::with('kategori')->paginate(10);

        // FILTER kategori berdasarkan tahun sekarang
        $kategoris = Kategori::whereHas('tahun', function ($query) use ($tahunSekarang) {
                $query->where('tahun', $tahunSekarang);
            })
            ->with('tahun')
            ->get();

        return view('bpublik.index', compact('publicBodies', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_badan' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id'
        ]);

        PublicBody::create([
            'nama_badan' => $request->nama_badan,
            'kategori_id' => $request->kategori_id,
            'is_registered' => false
        ]);

        return redirect()
            ->route('superadmin.bpublik.index')
            ->with('success', 'Badan publik berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(public_body $public_body)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(public_body $public_body)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_badan' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id'
        ]);

        $publicBody = PublicBody::findOrFail($id);

        $publicBody->update([
            'nama_badan' => $request->nama_badan,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()
            ->route('superadmin.bpublik.index')
            ->with('success', 'Badan publik berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $publicBody = PublicBody::findOrFail($id);
        $publicBody->delete();

        return redirect()
            ->route('superadmin.bpublik.index')
            ->with('success', 'Badan publik berhasil dihapus');
    }
}

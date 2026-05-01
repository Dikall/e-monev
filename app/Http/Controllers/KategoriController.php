<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunSekarang = now()->year;

        $kategoris = Kategori::with('tahun')
            ->whereHas('tahun', function ($query) use ($tahunSekarang) {
                $query->where('tahun', $tahunSekarang);
            })
            ->latest()
            ->get();

        $tahuns = Tahun::orderBy('tahun', 'desc')->get();

        return view('kategori.index', compact('kategoris', 'tahuns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahuns = Tahun::orderBy('tahun', 'desc')->get();
        return view('kategori.create', compact('tahuns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'tahun_id'  => 'required|exists:tahuns,id',
        ]);

        Kategori::create([
            'name'      => $request->name,
            'tahun_id'  => $request->tahun_id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kategori $kategori)
    {
        return view('kategori.show', compact('kategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        $tahuns = Tahun::orderBy('tahun', 'desc')->get();
        return view('kategori.edit', compact('kategori', 'tahuns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
       $request->validate([
            'name'      => 'required|string|max:255',
            'tahun_id'  => 'required|exists:tahuns,id',
        ]);

        $kategori->update([
            'name'      => $request->name,
            'tahun_id'  => $request->tahun_id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
       $kategori->delete();

        return redirect()
            ->back()
            ->with('success', 'Kategori berhasil dihapus');
    }
}

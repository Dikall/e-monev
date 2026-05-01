<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenggat;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TenggatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunSekarang = now()->year;

        $tenggats = Tenggat::with('kategori.tahun')
            ->latest()
            ->get();

        // FILTER: belum punya tenggat + tahun sekarang
        $kategoris = Kategori::whereHas('tahun', function ($query) use ($tahunSekarang) {
                $query->where('tahun', $tahunSekarang);
            })
            ->doesntHave('tenggat')
            ->with('tahun')
            ->get();

        return view('tenggat.index', compact('tenggats', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenggat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id|unique:tenggats,kategori_id',
            'tanggal_aktif' => 'required|date',
            'jam_aktif' => 'required',
            'tanggal_nonaktif' => 'required|date|after_or_equal:tanggal_aktif',
            'jam_nonaktif' => 'required',
        ]);

        $waktuAktif = Carbon::parse(
            $request->tanggal_aktif . ' ' . $request->jam_aktif
        );

        $waktuNonaktif = Carbon::parse(
            $request->tanggal_nonaktif . ' ' . $request->jam_nonaktif
        );

        Tenggat::create([
            'kategori_id' => $request->kategori_id,
            'waktu_aktif' => $waktuAktif,
            'waktu_nonaktif' => $waktuNonaktif,
        ]);

        return redirect()->back()
            ->with('success', 'Tenggat berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenggat $tenggat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenggat = TenggatKuesioner::findOrFail($id);
        return view('tenggat.edit', compact('tenggat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenggat $tenggat)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'tanggal_aktif' => 'required|date',
            'jam_aktif' => 'required',
            'tanggal_nonaktif' => 'required|date|after_or_equal:tanggal_aktif',
            'jam_nonaktif' => 'required',
        ]);

        $waktuAktif = Carbon::parse(
            $request->tanggal_aktif . ' ' . $request->jam_aktif
        );

        $waktuNonaktif = Carbon::parse(
            $request->tanggal_nonaktif . ' ' . $request->jam_nonaktif
        );

        $tenggat->update([
            'kategori_id' => $request->kategori_id,
            'waktu_aktif' => $waktuAktif,
            'waktu_nonaktif' => $waktuNonaktif,
        ]);

        return redirect()->back()
            ->with('success', 'Tenggat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenggat $tenggat)
    {
        $tenggat->delete();

        return redirect()->back()
            ->with('success', 'Tenggat berhasil dihapus');
    }
}

<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Jawaban;
use App\Models\Kategori;
use App\Models\Indikator;
use App\Models\Pertanyaan;
use App\Models\Tahun;
use App\Models\Tenggat;
use App\Models\Penilaian; // Sesuaikan dengan model penilaian Anda
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
 
class BadanPublikController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-beranda', ['only' => ['dashboard']]);
    }
 
    public function dashboard()
    {
        return view('badanpublik.beranda');
    }
 
    // ─────────────────────────────────────────────────────────
    // TAB KUESIONER — Halaman ringkasan + tombol Edit / Submit
    // ─────────────────────────────────────────────────────────
    public function kuesionerTab(Request $request)
    {
        $user = Auth::user();
 
        // Cek verifikasi akun
        if (!$user->is_aktif) {
            return view('badanpublik.kuesioner.tab_beranda_kuesioner', ['tidak_aktif' => true]);
        }
 
        $publicBody = $user->publicBody;
        if (!$publicBody) abort(403, 'Badan publik tidak ditemukan.');
 
        $tahunSekarang = now()->year;
        $tahun         = Tahun::where('tahun', $tahunSekarang)->first();
        if (!$tahun) abort(403, 'Tahun aktif tidak ditemukan.');
 
        $kategoriAktif = $publicBody->kategori;
        $kategoriId    = $kategoriAktif?->id;
        if (!$kategoriId) abort(403, 'Badan publik belum memiliki kategori.');
 
        $tenggat  = Tenggat::where('kategori_id', $kategoriId)->first();
        $now      = now();
        $isOpen   = $tenggat
            && $now->gte($tenggat->waktu_aktif)
            && $now->lte($tenggat->waktu_nonaktif);
        $isClosed = $tenggat && $now->gt($tenggat->waktu_nonaktif);
 
        // Cek apakah sudah di-submit
        $sudahSubmit = Jawaban::where('public_body_id', $publicBody->id)
            ->where('tahun_id', $tahun->id)
            ->where('is_submitted', true)
            ->exists();
 
        // Hitung total pertanyaan & yang sudah dijawab
        $indikators   = Indikator::where('tahun_id', $tahun->id)
            ->where('kategori_id', $kategoriId)
            ->get();
        $pertanyaanIds = Pertanyaan::where('level', 'pertanyaan')
            ->whereIn('indikator_id', $indikators->pluck('id'))
            ->pluck('id');
 
        $totalPertanyaan = $pertanyaanIds->count();
        $totalDijawab    = Jawaban::where('public_body_id', $publicBody->id)
            ->where('tahun_id', $tahun->id)
            ->whereIn('pertanyaan_id', $pertanyaanIds)
            ->whereNotNull('jawaban')
            ->count();
 
        return view('badanpublik.kuesioner.tab_beranda_kuesioner', compact(
            'user', 'publicBody', 'kategoriAktif', 'tahun',
            'tenggat', 'isOpen', 'isClosed',
            'sudahSubmit', 'totalPertanyaan', 'totalDijawab',
        ));
    }
 
    // ─────────────────────────────────────────────────────────
    // SUBMIT KUESIONER — Kunci jawaban (is_submitted = true)
    // ─────────────────────────────────────────────────────────
    public function submitKuesioner(Request $request)
    {
        $user = Auth::user();
 
        if (!$user->is_aktif) {
            return back()->with('error', 'Akun Anda belum aktif.');
        }
 
        $publicBody = $user->publicBody;
        if (!$publicBody) {
            return back()->with('error', 'Badan publik tidak ditemukan.');
        }
 
        $tahunSekarang = now()->year;
        $tahun         = Tahun::where('tahun', $tahunSekarang)->firstOrFail();
 
        // Validasi tenggat
        $kategoriId = $publicBody->kategori?->id;
        $tenggat    = Tenggat::where('kategori_id', $kategoriId)->first();
        $now        = now();
 
        if (!$tenggat
            || !$now->gte($tenggat->waktu_aktif)
            || !$now->lte($tenggat->waktu_nonaktif)
        ) {
            return back()->with('error',
                'Periode pengisian kuesioner sudah berakhir atau belum dibuka.'
            );
        }
 
        // Tandai semua jawaban milik public body ini sebagai submitted
        Jawaban::where('public_body_id', $publicBody->id)
            ->where('tahun_id', $tahun->id)
            ->update([
                'is_submitted'  => true,
                'submitted_at'  => now(),
            ]);
 
        return redirect()
            ->route('kuesioner.tab')
            ->with('success', 'Kuesioner berhasil di-submit. Jawaban telah dikunci.');
    }
 
    // ─────────────────────────────────────────────────────────
    // HASIL PENILAIAN
    // ─────────────────────────────────────────────────────────
    public function hasilPenilaian()
    {
        $user = Auth::user();
 
        if (!$user->is_aktif) {
            return view('badanpublik.kuesioner.hasil_penilaian_kuesioner', ['tidak_aktif' => true]);
        }
 
        $publicBody = $user->publicBody;
        if (!$publicBody) abort(403, 'Badan publik tidak ditemukan.');
 
        $tahunSekarang = now()->year;
        $tahun         = Tahun::where('tahun', $tahunSekarang)->first();
        if (!$tahun) abort(403, 'Tahun aktif tidak ditemukan.');
 
        $kategoriAktif = $publicBody->kategori;
 
        // Ambil penilaian — sesuaikan dengan model/struktur Anda
        // Contoh: Penilaian::where('public_body_id', ...)->with('indikator')->get()
        $penilaian = null;
        $sudahDinilai = false;
 
        // Jika Anda punya model Penilaian:
        // $penilaian = Penilaian::where('public_body_id', $publicBody->id)
        //     ->where('tahun_id', $tahun->id)
        //     ->with('indikator')
        //     ->first();
        // $sudahDinilai = $penilaian !== null;
 
        // Ambil jawaban untuk ditampilkan ringkasan per indikator
        $indikators = Indikator::where('tahun_id', $tahun->id)
            ->where('kategori_id', $kategoriAktif?->id)
            ->orderBy('no')
            ->get();
 
        $ringkasanPerIndikator = [];
        foreach ($indikators as $ind) {
            $pertanyaanIds = Pertanyaan::where('level', 'pertanyaan')
                ->where('indikator_id', $ind->id)
                ->pluck('id');
 
            $jawabans = Jawaban::where('public_body_id', $publicBody->id)
                ->where('tahun_id', $tahun->id)
                ->whereIn('pertanyaan_id', $pertanyaanIds)
                ->get();
 
            $totalBobot  = Pertanyaan::whereIn('id', $pertanyaanIds)->sum('bobot');
            $bobotYa     = Pertanyaan::whereIn('id',
                    $jawabans->where('jawaban', 1)->pluck('pertanyaan_id')
                )->sum('bobot');
 
            $ringkasanPerIndikator[] = [
                'indikator'   => $ind,
                'total'       => $pertanyaanIds->count(),
                'dijawab_ya'  => $jawabans->where('jawaban', 1)->count(),
                'dijawab_tidak' => $jawabans->where('jawaban', 0)->count(),
                'bobot_ya'    => $bobotYa,
                'total_bobot' => $totalBobot,
                'persentase'  => $totalBobot > 0 ? round(($bobotYa / $totalBobot) * 100, 2) : 0,
            ];
        }
 
        return view('badanpublik.kuesioner.hasil_penilaian_kuesioner', compact(
            'user', 'publicBody', 'kategoriAktif', 'tahun',
            'penilaian', 'sudahDinilai', 'ringkasanPerIndikator',
        ));
    }
 
    // ─────────────────────────────────────────────────────────
    // Metode lain (bawaan resource)
    // ─────────────────────────────────────────────────────────
    public function index(): View
    {
        return view('badanpublik.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

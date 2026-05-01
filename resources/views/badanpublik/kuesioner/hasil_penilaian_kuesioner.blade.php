@extends('components.layouts.app')

@section('content')

<div class="p-6 px-16">

    {{-- HEADER KEMBALI --}}
    <div class="mb-8">
        <a href="{{ route('kuesioner.tab') }}"
           class="inline-flex items-center gap-1 text-red-700 font-semibold hover:text-red-900 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <h2 class="text-red-700 font-bold text-lg mb-6">Hasil Penilaian</h2>

    {{-- AKUN BELUM AKTIF --}}
    @if(isset($tidak_aktif) && $tidak_aktif)
        <div class="flex flex-col items-center justify-center py-32 text-center">
            <div class="bg-white rounded-2xl shadow-md border border-red-100 px-12 py-16 max-w-lg">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-700"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M12 3C6.477 3 2 7.477 2 12s4.477 9 10 9
                                 10-4.477 10-9S17.523 3 12 3z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">Akun Belum Aktif</h2>
                <p class="text-gray-500 leading-relaxed">
                    Lakukan Verifikasi Akun Terlebih Dahulu untuk dapat mengakses halaman ini.
                    Silakan hubungi administrator untuk mengaktifkan akun Anda.
                </p>
            </div>
        </div>

    @else

    {{-- INFORMASI BADAN PUBLIK --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden mb-6">
        <div class="px-8 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
                Informasi Responden
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-12">
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-gray-400 font-medium">Nama Badan Publik</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $publicBody->nama_badan_publik ?? '-' }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-gray-400 font-medium">Kategori</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $kategoriAktif->nama_kategori ?? '-' }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-gray-400 font-medium">Tahun Penilaian</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $tahun->tahun ?? '-' }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-gray-400 font-medium">Nama Responden</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $user->name }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- PENILAIAN BELUM ADA --}}
    @if(!$sudahDinilai)
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="bg-white rounded-2xl shadow-sm border border-yellow-100 px-12 py-16 max-w-lg">

                {{-- Ikon animasi loading/proses --}}
                <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-yellow-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-3">
                    Sedang Dalam Proses Penilaian
                </h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Kuesioner Anda telah berhasil di-submit dan sedang dalam proses penilaian
                    oleh administrator. Hasil penilaian akan ditampilkan di sini setelah
                    proses selesai.
                </p>
                <p class="text-gray-400 text-xs mt-4">
                    Silakan kunjungi halaman ini kembali secara berkala untuk mengecek
                    status penilaian Anda.
                </p>

            </div>
        </div>

    @else
    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- PENILAIAN SUDAH ADA — Tampilkan hasil                 --}}
    {{-- ══════════════════════════════════════════════════════ --}}

        {{-- Skor Total --}}
        @if($penilaian)
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden mb-6">
            <div class="px-8 py-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">
                    Skor Total Penilaian
                </h3>
                <div class="flex items-center gap-8">
                    {{-- Lingkaran skor --}}
                    <div class="relative w-28 h-28 flex-shrink-0">
                        <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                            <circle cx="60" cy="60" r="50"
                                    fill="none" stroke="#f3f4f6" stroke-width="10"/>
                            <circle cx="60" cy="60" r="50"
                                    fill="none" stroke="#b91c1c" stroke-width="10"
                                    stroke-dasharray="{{ round(($penilaian->skor_total / 100) * 314.16, 2) }} 314.16"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-red-700">
                                {{ number_format($penilaian->skor_total, 2) }}
                            </span>
                            <span class="text-xs text-gray-400">/ 100</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-3xl font-bold text-gray-900 mb-1">
                            {{ $penilaian->predikat ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Predikat Informatif sesuai standar KIP
                        </p>
                        @if($penilaian->catatan)
                            <p class="mt-3 text-sm text-gray-600 bg-gray-50 border border-gray-200
                                      rounded-lg px-4 py-2 max-w-md">
                                <span class="font-medium text-gray-700">Catatan: </span>
                                {{ $penilaian->catatan }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tabel per Indikator --}}
        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden mb-6">
            <div class="px-8 py-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    Rincian Per Indikator
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-red-700 text-white">
                            <th class="px-6 py-3 text-left font-semibold">No</th>
                            <th class="px-6 py-3 text-left font-semibold">Indikator</th>
                            <th class="px-6 py-3 text-center font-semibold">Dijawab Ya</th>
                            <th class="px-6 py-3 text-center font-semibold">Dijawab Tidak</th>
                            <th class="px-6 py-3 text-center font-semibold">Bobot Tercapai</th>
                            <th class="px-6 py-3 text-center font-semibold">Total Bobot</th>
                            <th class="px-6 py-3 text-center font-semibold">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ringkasanPerIndikator as $index => $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-600 font-medium">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-gray-800">
                                    {{ $item['indikator']->nama_indikator }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8
                                                 bg-green-100 text-green-700 rounded-full font-semibold text-xs">
                                        {{ $item['dijawab_ya'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8
                                                 bg-red-100 text-red-700 rounded-full font-semibold text-xs">
                                        {{ $item['dijawab_tidak'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 font-medium">
                                    {{ $item['bobot_ya'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500">
                                    {{ $item['total_bobot'] }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $persen = $item['persentase'];
                                        $colorClass = $persen >= 80 ? 'text-green-700 bg-green-50 border-green-200'
                                            : ($persen >= 50 ? 'text-yellow-700 bg-yellow-50 border-yellow-200'
                                            : 'text-red-700 bg-red-50 border-red-200');
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-full border text-xs font-semibold {{ $colorClass }}">
                                        {{ $persen }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">
                                    Tidak ada data indikator.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif
    {{-- end sudahDinilai --}}

    @endif
    {{-- end akun aktif --}}

</div>

@endsection
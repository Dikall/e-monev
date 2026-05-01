{{--
    Partial: _baris_pertanyaan.blade.php
    Path: resources/views/badanpublik/kuesioner/_baris_pertanyaan.blade.php
--}}

@php
    $jawaban     = $jawabans[$pertanyaan->id] ?? null;
    $disabled    = $isClosed || !$isOpen;
    $indentClass = $indent ?? 'pl-8';

    // Nilai jawaban: cast ke int agar perbandingan konsisten
    // DB bisa return string "0"/"1", nullable, atau integer
    $nilaiJawaban = $jawaban ? (int) $jawaban->jawaban : null;

    // Links: sudah di-cast array di model, tinggal join
    $linksString = '';
    if ($jawaban && !empty($jawaban->links) && is_array($jawaban->links)) {
        $linksString = implode(', ', $jawaban->links);
    }

    // Nama file dokumen yang sudah ada
    $namaFile = $jawaban?->dokumen_path ? basename($jawaban->dokumen_path) : '';
@endphp

<tr class="hover:bg-gray-50 transition-colors">

    {{-- NOMOR --}}
    <td class="px-4 py-3 text-gray-600 align-top {{ $indentClass }}">
        {{ $pertanyaan->nomor }}
    </td>

    {{-- PERTANYAAN --}}
    <td class="px-4 py-3 text-gray-700 align-top leading-relaxed">
        {{ $pertanyaan->pertanyaan_kuisioner }}
    </td>

    {{-- PILIHAN JAWABAN --}}
    <td class="px-4 py-3 align-top">
        <div class="flex items-center gap-3 mt-1">

            {{-- Ya --}}
            <label class="flex items-center gap-1
                {{ $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}">
                <input type="radio"
                       name="jawaban[{{ $pertanyaan->id }}]"
                       value="1"
                       @checked($nilaiJawaban === 1)
                       @disabled($disabled)
                       class="text-red-700 focus:ring-red-700">
                <span class="text-sm text-gray-700">Ya</span>
            </label>

            {{-- Tidak --}}
            <label class="flex items-center gap-1
                {{ $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}">
                <input type="radio"
                       name="jawaban[{{ $pertanyaan->id }}]"
                       value="0"
                       @checked($nilaiJawaban === 0 && $jawaban !== null)
                       @disabled($disabled)
                       class="text-red-700 focus:ring-red-700">
                <span class="text-sm text-gray-700">Tidak</span>
            </label>

        </div>
    </td>

    {{-- DATA PENDUKUNG: LINK --}}
    <td class="px-3 py-3 align-top border-l border-gray-100">
        <input type="text"
               name="links[{{ $pertanyaan->id }}]"
               value="{{ $linksString }}"
               placeholder="Masukkan Link..."
               @disabled($disabled)
               class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-xs text-gray-700
                      placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-red-400
                      {{ $disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white' }}">
        @if(!$disabled)
            <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma atau spasi</p>
        @endif
    </td>

    {{-- DATA PENDUKUNG: UPLOAD DOKUMEN --}}
    <td class="px-3 py-3 align-top border-l border-gray-100"
        x-data="{ fileName: '{{ $namaFile }}' }">

        <div class="flex items-start gap-2">

            @if(!$disabled)
            <label class="flex-shrink-0 cursor-pointer">
                <div class="flex items-center gap-1.5 bg-red-700 hover:bg-red-800 text-white
                            text-xs font-medium px-3 py-1.5 rounded-md transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Pilih File
                </div>
                <input type="file"
                       name="dokumen[{{ $pertanyaan->id }}]"
                       accept="application/pdf"
                       class="hidden"
                       @change="fileName = $event.target.files[0]?.name || ''">
            </label>
            @endif

            <div class="min-w-0">
                <p class="text-xs text-gray-500 break-all"
                   x-text="fileName || 'Tidak ada file dipilih'"></p>
                <p class="text-xs text-red-400 mt-0.5">Maksimum ukuran file 5MB</p>

                @if($jawaban?->dokumen_path)
                <a href="{{ Storage::url($jawaban->dokumen_path) }}"
                   target="_blank"
                   class="text-xs text-red-700 underline hover:text-red-900 mt-1 block">
                    Lihat Dokumen
                </a>
                @endif
            </div>

        </div>
    </td>

</tr>
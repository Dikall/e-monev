@extends('components.layouts.app')

@section('content')

<div class="ml-64 p-6"
    x-data="pertanyaanComponent()"
    x-init="
        @if(old('mode') === 'tambah') openTambah = true @endif
        @if(old('mode') === 'edit')   openEdit   = true @endif
    ">

    {{-- ================= FILTER ================= --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <form method="GET">
            <div class="grid grid-cols-3 gap-4 mb-4">

                <select name="tahun_id" x-model="tahun" @change="filterKategori"
                    class="border p-3 rounded">
                    <option value="">Tahun</option>
                    @foreach($tahuns as $t)
                        <option value="{{ $t->id }}" {{ request('tahun_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->tahun }}
                        </option>
                    @endforeach
                </select>

                <select name="kategori_id" x-model="kategori" @change="filterIndikator"
                    :disabled="!tahun" class="border p-3 rounded">
                    <option value="">Kategori</option>
                    <template x-for="k in kategoris" :key="k.id">
                        <option :value="k.id" x-text="k.name"></option>
                    </template>
                </select>

                <select name="indikator_id" x-model="indikator"
                    :disabled="!kategori" class="border p-3 rounded">
                    <option value="">Indikator</option>
                    <template x-for="i in indikators" :key="i.id">
                        <option :value="i.id" x-text="i.nama_indikator"></option>
                    </template>
                </select>

            </div>
            <button class="w-full bg-red-700 text-white p-3 rounded">Tampilkan</button>
        </form>
    </div>

    {{-- ================= ALERT ================= --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex justify-between mb-6">
        <h1 class="text-xl font-bold">Data Pertanyaan</h1>
        <button @click="openTambah = true; resetForm();"
            class="bg-red-700 text-white px-4 py-2 rounded">
            + Tambah Pertanyaan
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-red-700 text-white">
                <tr>
                    <th class="p-3 text-left">Tahun</th>
                    <th class="p-3 text-left">Kategori</th>
                    <th class="p-3 text-left">Indikator</th>
                    <th class="p-3 text-left">Level</th>
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Pertanyaan</th>
                    <th class="p-3 text-left">Bobot</th>
                    <th class="p-3 text-left">Bobot Normal</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pertanyaans as $p)
                <tr class="border-t
                    {{ $p->level === 'judul'    ? 'bg-gray-100 font-bold' : '' }}
                    {{ $p->level === 'subjudul' ? 'bg-gray-50  font-semibold' : '' }}
                ">
                    <td class="p-3">{{ $p->tahun->tahun }}</td>
                    <td class="p-3">{{ $p->kategori->name }}</td>
                    <td class="p-3">{{ $p->indikator->nama_indikator }}</td>
                    <td class="p-3">
                        @if($p->level === 'judul')
                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">Judul</span>
                        @elseif($p->level === 'subjudul')
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs">Sub Judul</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Pertanyaan</span>
                        @endif
                    </td>
                    <td class="p-3">{{ $p->nomor }}</td>
                    <td class="p-3">{{ $p->pertanyaan_kuisioner }}</td>
                    <td class="p-3 text-center">
                        {{ $p->level === 'pertanyaan' ? $p->bobot : '-' }}
                    </td>
                    <td class="p-3 text-center">
                        @if($p->level === 'pertanyaan')
                            @php
                                $totalInd = $bobotPerIndikator[$p->indikator_id] ?? 0;
                                $normal   = $totalInd > 0
                                    ? round(($p->bobot / $totalInd) * $p->indikator->bobot, 2)
                                    : 0;
                            @endphp
                            {{ $normal }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-3 text-center space-x-2">
                        {{--
                            PERBAIKAN: Hapus filterParentByLevel() dari sini
                            karena fungsi itu mereset parent_id ke '',
                            sehingga parent_id yang di-set setelahnya tidak akan
                            pernah tersimpan dengan benar.
                            parent_id langsung di-set setelah levelForm.
                        --}}
                        <button
                            @click='
                                openEdit      = true;
                                pertanyaanId  = {{ $p->id }};
                                tahunForm     = "{{ $p->tahun_id }}";
                                filterKategoriForm(false);
                                kategoriForm  = "{{ $p->kategori_id }}";
                                filterIndikatorForm(false);
                                indikatorForm = "{{ $p->indikator_id }}";
                                levelForm     = "{{ $p->level }}";
                                parent_id     = "{{ $p->parent_id ?? '' }}";
                                nomor         = "{{ $p->nomor }}";
                                pertanyaan    = `{{ addslashes($p->pertanyaan_kuisioner) }}`;
                                bobot         = "{{ $p->bobot }}";
                            '
                            class="bg-red-700 text-white px-3 py-1 rounded">
                            Edit
                        </button>

                        <form action="{{ route('superadmin.pertanyaan.destroy', $p->id) }}"
                            method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="border border-red-600 text-red-600 px-3 py-1 rounded">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-6 text-center text-gray-500">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ===================== MODAL TAMBAH ===================== --}}
    <div x-show="openTambah" x-cloak
        class="fixed inset-0 bg-black/40 backdrop-blur-sm overflow-y-auto z-50">
        <div class="flex items-start justify-center min-h-screen py-10 px-4">
            <div class="bg-white w-[800px] p-6 rounded-xl shadow-lg">

                <div class="flex justify-between mb-6">
                    <h2 class="text-xl font-bold">Tambah Pertanyaan</h2>
                    <button @click="openTambah=false; resetForm()">✕</button>
                </div>

                <form action="{{ route('superadmin.pertanyaan.store') }}" method="POST">
                    @csrf

                    {{-- Tahun --}}
                    <label class="font-semibold block mb-1">Tahun</label>
                    <select name="tahun_id" x-model="tahunForm" @change="filterKategoriForm()"
                        class="w-full border p-3 rounded mb-4">
                        <option value="">Pilih Tahun</option>
                        @foreach($tahuns as $t)
                            <option value="{{ $t->id }}">{{ $t->tahun }}</option>
                        @endforeach
                    </select>

                    {{-- Kategori --}}
                    <label class="font-semibold block mb-1">Kategori</label>
                    <select name="kategori_id" x-model="kategoriForm" @change="filterIndikatorForm()"
                        class="w-full border p-3 rounded mb-4">
                        <option value="">Pilih Kategori</option>
                        <template x-for="k in kategoris" :key="k.id">
                            <option :value="k.id" x-text="k.name"></option>
                        </template>
                    </select>

                    {{-- Indikator --}}
                    <label class="font-semibold block mb-1">Indikator</label>
                    <select name="indikator_id" x-model="indikatorForm"
                        @change="indikatorForm = $event.target.value; parent_id = '';"
                        class="w-full border p-3 rounded mb-4">
                        <option value="">Pilih Indikator</option>
                        <template x-for="i in indikators" :key="i.id">
                            <option :value="i.id" x-text="i.nama_indikator"></option>
                        </template>
                    </select>

                    {{-- Level --}}
                    <label class="font-semibold block mb-1">Level</label>
                    <select name="level" x-model="levelForm"
                        @change="levelForm = $event.target.value; parent_id = '';"
                        class="w-full border p-3 rounded mb-4">
                        <option value="judul">Judul (I)</option>
                        <option value="subjudul">Sub Judul (A)</option>
                        <option value="pertanyaan">Pertanyaan (1)</option>
                    </select>

                    {{--
                        PERBAIKAN UTAMA:
                        - Hapus name="parent_id" dari kedua <select> di atas
                        - Gunakan satu <input type="hidden" name="parent_id"> di bawah
                        - Ini mencegah browser mengirim dua nilai parent_id sekaligus
                          (karena x-show hanya menyembunyikan secara visual, bukan
                          menghapus elemen dari DOM)
                    --}}

                    {{-- Dropdown parent Judul (hanya tampil saat level = subjudul) --}}
                    <div x-show="levelForm === 'subjudul'" class="mb-4">
                        <label class="font-semibold block mb-1">
                            Parent Judul <span class="text-red-600">*</span>
                        </label>
                        <select x-model="parent_id" class="w-full border p-3 rounded">
                            <option value="">-- Pilih Judul --</option>
                            <template x-for="p in parentJudulFiltered" :key="p.id">
                                <option :value="p.id"
                                    x-text="p.nomor + ' - ' + p.pertanyaan_kuisioner">
                                </option>
                            </template>
                        </select>
                        <p class="text-xs text-red-500 mt-1"
                           x-show="levelForm === 'subjudul' && !parent_id">
                            Wajib pilih Judul sebagai parent.
                        </p>
                    </div>

                    {{-- Dropdown parent Sub Judul (hanya tampil saat level = pertanyaan) --}}
                    <div x-show="levelForm === 'pertanyaan'" class="mb-4">
                        <label class="font-semibold block mb-1">Parent Sub Judul</label>
                        <select x-model="parent_id" class="w-full border p-3 rounded">
                            <option value="">-- Tanpa Sub Judul --</option>
                            <template x-for="p in parentSubJudulFiltered" :key="p.id">
                                <option :value="p.id"
                                    x-text="p.nomor + ' - ' + p.pertanyaan_kuisioner">
                                </option>
                            </template>
                        </select>
                    </div>

                    {{-- Satu hidden input yang akan disubmit ke server --}}
                    <input type="hidden" name="parent_id" :value="parent_id">

                    {{-- Nomor --}}
                    <label class="font-semibold block mb-1">Nomor</label>
                    <input name="nomor" x-model="nomor"
                        class="w-full border p-3 rounded mb-4"
                        placeholder="Contoh: I, A, 1">

                    {{-- Teks --}}
                    <label class="font-semibold block mb-1">
                        <span x-show="levelForm==='judul'">Judul</span>
                        <span x-show="levelForm==='subjudul'">Sub Judul</span>
                        <span x-show="levelForm==='pertanyaan'">Pertanyaan</span>
                    </label>
                    <textarea name="pertanyaan_kuisioner" x-model="pertanyaan"
                        class="w-full border p-3 rounded mb-4" rows="3"></textarea>

                    {{-- Bobot --}}
                    <div x-show="levelForm === 'pertanyaan'">
                        <div class="flex justify-between items-center mb-1">
                            <label class="font-semibold">Bobot Soal</label>
                            <div class="text-sm"
                                :class="totalBobotSekarang > 100 ? 'text-red-600 font-bold' : 'text-gray-600'">
                                Total bobot indikator ini:
                                <span x-text="totalBobotSekarang"></span> / 100
                                <span x-show="totalBobotSekarang > 100"> ⚠ Melebihi 100!</span>
                            </div>
                        </div>
                        <input name="bobot" x-model="bobot"
                            @input="bobotInput = parseInt($event.target.value) || 0"
                            class="w-full border p-3 rounded mb-1"
                            placeholder="Total bobot semua soal dalam indikator = 100">
                        <p class="text-xs text-gray-500 mb-4">
                            Bobot aktual = (<span x-text="bobot || 0"></span> / 100) × bobot indikator
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button
                            :disabled="levelForm === 'pertanyaan' && totalBobotSekarang > 100"
                            :class="(levelForm === 'pertanyaan' && totalBobotSekarang > 100)
                                ? 'bg-gray-400 cursor-not-allowed'
                                : 'bg-red-700 hover:bg-red-800'"
                            class="text-white px-6 py-2 rounded">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL EDIT ===================== --}}
    <div x-show="openEdit" x-cloak
        class="fixed inset-0 bg-black/40 backdrop-blur-sm overflow-y-auto z-50">
        <div class="flex items-start justify-center min-h-screen py-10 px-4">
            <div class="bg-white w-[800px] p-6 rounded-xl shadow-lg">

                <div class="flex justify-between mb-6">
                    <h2 class="text-xl font-bold">Edit Pertanyaan</h2>
                    <button @click="openEdit=false">✕</button>
                </div>

                <form :action="'/superadmin/pertanyaan/' + pertanyaanId" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="font-semibold block mb-1">Tahun</label>
                    <select name="tahun_id" x-model="tahunForm"
                        @change="filterKategoriForm()"
                        class="w-full border p-3 rounded mb-4">
                        @foreach($tahuns as $t)
                            <option value="{{ $t->id }}">{{ $t->tahun }}</option>
                        @endforeach
                    </select>

                    <label class="font-semibold block mb-1">Kategori</label>
                    <select name="kategori_id" x-model="kategoriForm"
                        @change="filterIndikatorForm()"
                        class="w-full border p-3 rounded mb-4">
                        <template x-for="k in kategoris" :key="k.id">
                            <option :value="k.id" x-text="k.name"></option>
                        </template>
                    </select>

                    <label class="font-semibold block mb-1">Indikator</label>
                    <select name="indikator_id" x-model="indikatorForm"
                        @change="indikatorForm = $event.target.value; parent_id = '';"
                        class="w-full border p-3 rounded mb-4">
                        <template x-for="i in indikators" :key="i.id">
                            <option :value="i.id" x-text="i.nama_indikator"></option>
                        </template>
                    </select>

                    <label class="font-semibold block mb-1">Level</label>
                    <select name="level" x-model="levelForm"
                        @change="levelForm = $event.target.value; parent_id = '';"
                        class="w-full border p-3 rounded mb-4">
                        <option value="judul">Judul (I)</option>
                        <option value="subjudul">Sub Judul (A)</option>
                        <option value="pertanyaan">Pertanyaan (1)</option>
                    </select>

                    {{--
                        PERBAIKAN SAMA SEPERTI MODAL TAMBAH:
                        Dropdown hanya untuk tampilan (tanpa name),
                        satu hidden input yang submit ke server.
                    --}}

                    <div x-show="levelForm === 'subjudul'" class="mb-4">
                        <label class="font-semibold block mb-1">
                            Parent Judul <span class="text-red-600">*</span>
                        </label>
                        <select x-model="parent_id" class="w-full border p-3 rounded">
                            <option value="">-- Pilih Judul --</option>
                            <template x-for="p in parentJudulFiltered" :key="p.id">
                                <option :value="p.id"
                                    x-text="p.nomor + ' - ' + p.pertanyaan_kuisioner">
                                </option>
                            </template>
                        </select>
                    </div>

                    <div x-show="levelForm === 'pertanyaan'" class="mb-4">
                        <label class="font-semibold block mb-1">Parent Sub Judul</label>
                        <select x-model="parent_id" class="w-full border p-3 rounded">
                            <option value="">-- Tanpa Sub Judul --</option>
                            <template x-for="p in parentSubJudulFiltered" :key="p.id">
                                <option :value="p.id"
                                    x-text="p.nomor + ' - ' + p.pertanyaan_kuisioner">
                                </option>
                            </template>
                        </select>
                    </div>

                    {{-- Satu hidden input yang akan disubmit ke server --}}
                    <input type="hidden" name="parent_id" :value="parent_id">

                    <label class="font-semibold block mb-1">Nomor</label>
                    <input name="nomor" x-model="nomor"
                        class="w-full border p-3 rounded mb-4">

                    <label class="font-semibold block mb-1">
                        <span x-show="levelForm==='judul'">Judul</span>
                        <span x-show="levelForm==='subjudul'">Sub Judul</span>
                        <span x-show="levelForm==='pertanyaan'">Pertanyaan</span>
                    </label>
                    <textarea name="pertanyaan_kuisioner" x-model="pertanyaan"
                        class="w-full border p-3 rounded mb-4" rows="3"></textarea>

                    <div x-show="levelForm === 'pertanyaan'">
                        <label class="font-semibold block mb-1">Bobot Soal</label>
                        <input name="bobot" x-model="bobot"
                            class="w-full border p-3 rounded mb-4">
                    </div>

                    <div class="flex justify-end">
                        <button class="bg-red-700 text-white px-6 py-2 rounded hover:bg-red-800">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function pertanyaanComponent() {
    return {
        openTambah: false,
        openEdit:   false,

        pertanyaanId: null,

        // Filter bar
        tahun:    '',
        kategori: '',
        indikator:'',

        // Modal form
        tahunForm:    '',
        kategoriForm: '',
        indikatorForm:'',
        levelForm:    'judul',  // default judul agar tidak muncul validasi parent saat pertama buka

        parent_id:  '',
        nomor:      '',
        pertanyaan: '',
        bobot:      '',
        bobotInput: 0,

        // Data dari blade
        kategorisAll:      @js($kategoris),
        indikatorsAll:     @js($indikators),
        parentJudulAll:    @js($parentJudul),
        parentSubJudulAll: @js($parentSubJudul),

        bobotPerIndikator: @js($bobotPerIndikator),

        kategoris:  [],
        indikators: [],

        get totalBobotAwal() {
            return parseInt(this.bobotPerIndikator[this.indikatorForm] ?? 0);
        },
        get totalBobotSekarang() {
            return this.totalBobotAwal + parseInt(this.bobotInput || 0);
        },

        // Parent terfilter berdasarkan indikator yang dipilih
        get parentJudulFiltered() {
            if (!this.indikatorForm) return [];
            return this.parentJudulAll.filter(p => p.indikator_id == this.indikatorForm);
        },
        get parentSubJudulFiltered() {
            if (!this.indikatorForm) return [];
            return this.parentSubJudulAll.filter(p => p.indikator_id == this.indikatorForm);
        },

        // Filter bar (halaman utama)
        filterKategori() {
            this.kategoris  = this.kategorisAll.filter(k => k.tahun_id == this.tahun);
            this.kategori   = '';
            this.indikator  = '';
            this.indikators = [];
        },
        filterIndikator() {
            this.indikators = this.indikatorsAll.filter(i => i.kategori_id == this.kategori);
            this.indikator  = '';
        },

        // Filter modal form
        filterKategoriForm(reset = true) {
            this.kategoris = this.kategorisAll.filter(k => k.tahun_id == this.tahunForm);
            if (reset) {
                this.kategoriForm  = '';
                this.indikators    = [];
                this.indikatorForm = '';
                this.parent_id     = '';
            }
        },
        filterIndikatorForm(reset = true) {
            this.indikators = this.indikatorsAll.filter(i => i.kategori_id == this.kategoriForm);
            if (reset) {
                this.indikatorForm = '';
                this.parent_id     = '';
            }
        },

        resetForm() {
            this.tahunForm     = '';
            this.kategoriForm  = '';
            this.indikatorForm = '';
            this.levelForm     = 'judul';
            this.parent_id     = '';
            this.nomor         = '';
            this.pertanyaan    = '';
            this.bobot         = '';
            this.bobotInput    = 0;
            this.kategoris     = [];
            this.indikators    = [];
        }
    }
}
</script>

@endsection
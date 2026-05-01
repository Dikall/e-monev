<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PublicBody;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-beranda', ['only' => ['dashboard', 'show']]);
        $this->middleware('permission:view-verifikator')->only('index');
        $this->middleware('permission:create-verifikator')->only('store');
        $this->middleware('permission:edit-verifikator')->only('update');
        $this->middleware('permission:delete-verifikator')->only('destroy');
    }

    public function index(): View
    {
        return view('verifikator.index', [
            'users'    => User::role('Admin')->with('publicBodies.kategori')->get(),
            'bodies'   => PublicBody::with('kategori')->get(),
            'kategoris' => \App\Models\Kategori::all(),
        ]);
    }

    public function dashboard()
    {
        return view('admin.beranda');
    }

    public function create()
    {
        //
    }

    /**
     * FIX 1: Hapus validasi wajib public_body_ids agar verifikator bisa
     *         dibuat tanpa badan publik dulu (set belakangan lewat modal Set).
     *         Jika ingin tetap wajib, tambahkan field hidden / multi-select
     *         di modal Tambah juga.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'telepon'  => $request->telepon,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('Admin');

        // Sync badan publik hanya jika dikirim
        if ($request->filled('public_body_ids')) {
            $user->publicBodies()->sync($request->public_body_ids);
        }

        return back()->with('success', 'Berhasil tambah verifikator');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    /**
     * FIX 2: Inisialisasi $data sebelum digunakan, hapus validasi duplikat,
     *         dan sertakan name + telepon dalam update.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'nullable|string|max:255',
            'telepon'  => 'nullable|string|max:20',
            'username' => 'required|string|max:100|unique:users,username,' . $id,
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Inisialisasi $data dengan field yang boleh diupdate
        $data = [
            'name'     => $request->name,
            'telepon'  => $request->telepon,
            'username' => $request->username,
            'email'    => $request->email,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sync badan publik hanya jika dikirim
        if ($request->filled('public_body_ids')) {
            $user->publicBodies()->sync($request->public_body_ids);
        }

        return back()->with('success', 'Berhasil update');
    }

    /**
     * FIX 3: Gunakan sync() — sudah benar. Masalah double entry ada di
     *         frontend (filterBodies tidak dipanggil), bukan di sini.
     */
    public function setPublicBody(Request $request, $id)
    {
        $request->validate([
            'public_body_ids' => 'required|array',
        ]);

        $user = User::findOrFail($id);

        // sync() otomatis hapus yang lama dan insert yang baru — tidak akan double
        $user->publicBodies()->sync($request->public_body_ids);

        return back()->with('success', 'Badan publik berhasil diset');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->syncRoles([]);
        $user->delete();

        return back()->with('success', 'Berhasil hapus');
    }
}
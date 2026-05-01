<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kategori;
use App\Models\PublicBody;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    protected $redirectTo = '/badanpublik/beranda';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Form register
    public function showRegistrationForm()
    {
        $kategoris = Kategori::all();
        return view('auth.register', compact('kategoris'));
    }

    // AJAX ambil badan publik
    public function getPublicBodies($categoryId)
    {
        $publicBodies = PublicBody::where('kategori_id', $categoryId)
            ->where('is_registered', false)
            ->orderBy('nama_badan')
            ->get(['id', 'nama_badan']);

        return response()->json($publicBodies);
    }

    // REGISTER USER
    public function register()
    {
        $data = request()->all();

        $validator = Validator::make($data, [

            'kategori_id' => ['required','exists:kategoris,id'],

            'public_body_id' => [
                'required',
                Rule::exists('public_bodies', 'id')
                    ->where(fn($q) =>
                        $q->where('kategori_id', $data['kategori_id'])
                          ->where('is_registered', false)
                    ),
            ],

            'alamat' => ['required','string','max:255'],
            'telepon' => ['nullable','string','max:20'],
            'website' => ['nullable','url','max:255'],

            'nama_responden' => ['required','string','max:255'],
            'jabatan_responden' => ['required','string','max:255'],
            'nohp_responden' => ['required','string','max:20'],
            'email_responden' => ['required','email','max:255'],

            'nama_ppid' => ['nullable','string','max:255'],
            'nohp_ppid' => ['nullable','string','max:20'],
            'email_ppid' => ['nullable','email','max:255'],

            'email' => ['required','email','unique:users,email'],
            'password' => ['required','min:8','confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($data) {

            $user = User::create([
                'public_body_id' => $data['public_body_id'],
                'alamat' => $data['alamat'],
                'telepon' => $data['telepon'] ?? null,
                'website' => $data['website'] ?? null,

                'email' => $data['email'],
                'password' => Hash::make($data['password']),

                'nama_responden' => $data['nama_responden'],
                'jabatan_responden' => $data['jabatan_responden'],
                'nohp_responden' => $data['nohp_responden'],
                'email_responden' => $data['email_responden'],

                'nama_ppid' => $data['nama_ppid'] ?? null,
                'nohp_ppid' => $data['nohp_ppid'] ?? null,
                'email_ppid' => $data['email_ppid'] ?? null,
            ]);

            // assign role
            $user->assignRole('Badan Publik');

            // tandai badan publik sudah register
            PublicBody::where('id', $data['public_body_id'])
                ->update(['is_registered' => true]);
        });

        return redirect('/login')->with('success','Registrasi berhasil, silakan login.');
    }
}
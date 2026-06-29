<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DosenRegisterController extends Controller
{
    public function create()
    {
        $fakultasList = Fakultas::with('prodi')->orderBy('nama')->get();
        return view('auth.register-dosen', compact('fakultasList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'nidn'     => ['required', 'string', 'max:20', 'unique:users,nidn', 'unique:dosen,nidn'],
            'nip'      => ['nullable', 'string', 'max:30', 'unique:users,nip'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'prodi_id' => ['required', 'exists:prodi,id'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'nidn.required'     => 'NIDN wajib diisi.',
            'nidn.unique'       => 'NIDN sudah terdaftar dalam sistem.',
            'nip.unique'        => 'NIP sudah terdaftar.',
            'prodi_id.required' => 'Program studi wajib dipilih.',
            'prodi_id.exists'   => 'Program studi tidak valid.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        // Buat akun user dengan status pending
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'nip'      => $data['nip'] ?? null,
            'nidn'     => $data['nidn'],
            'status'   => 'pending',
        ]);

        $user->assignRole('dosen');

        // Buat profil dosen sekaligus
        Dosen::create([
            'user_id'  => $user->id,
            'prodi_id' => $data['prodi_id'],
            'nidn'     => $data['nidn'],
            'nama'     => $data['name'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'] ?? null,
        ]);

        return redirect()->route('login')->with(
            'status',
            'Pendaftaran berhasil! Akun Anda sedang menunggu aktivasi oleh Admin P3KM. Anda akan bisa login setelah diaktifkan.'
        );
    }
}

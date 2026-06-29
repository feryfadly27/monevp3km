<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class IdentityPasswordResetController extends Controller
{
    // Langkah 1: Form input email
    public function requestForm()
    {
        return view('auth.forgot-password-identity');
    }

    // Langkah 1: Proses cek email
    public function requestSubmit(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Selalu tampilkan pesan sama agar tidak bocorkan info akun
        if (! $user || (! $user->nip && ! $user->nidn)) {
            return back()->with('status', 'Jika email terdaftar dan memiliki NIP/NIDN, langkah verifikasi akan muncul.');
        }

        // Buat token unik, simpan ke tabel
        $token = Str::random(64);

        DB::table('identity_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'verified' => false, 'created_at' => now()]
        );

        return redirect()->route('password.identity.verify', ['email' => $user->email, 'token' => $token]);
    }

    // Langkah 2: Form verifikasi NIP/NIDN
    public function verifyForm(Request $request)
    {
        if (! $request->email || ! $request->token) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link tidak valid. Silakan ulangi dari awal.']);
        }

        $record = $this->findValidToken($request->email, $request->token);

        if (! $record) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link tidak valid atau sudah kedaluwarsa. Silakan ulangi.']);
        }

        return view('auth.verify-identity', [
            'email' => $request->email,
            'token' => $request->token,
        ]);
    }

    // Langkah 2: Proses verifikasi NIP/NIDN
    public function verifySubmit(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'token'    => ['required'],
            'identity' => ['required', 'string'],
        ], [
            'identity.required' => 'NIP atau NIDN wajib diisi.',
        ]);

        $record = $this->findValidToken($request->email, $request->token);

        if (! $record) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();

        $identity = trim($request->identity);
        $match = ($user->nip && $user->nip === $identity)
              || ($user->nidn && $user->nidn === $identity);

        if (! $match) {
            return back()
                ->withInput($request->only('email', 'token'))
                ->withErrors(['identity' => 'NIP atau NIDN tidak sesuai dengan akun ini.']);
        }

        // Tandai token sebagai terverifikasi
        DB::table('identity_reset_tokens')
            ->where('email', $request->email)
            ->update(['verified' => true]);

        return redirect()->route('password.identity.reset', [
            'email' => $request->email,
            'token' => $request->token,
        ]);
    }

    // Langkah 3: Form reset password
    public function resetForm(Request $request)
    {
        if (! $request->email || ! $request->token) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link tidak valid. Silakan ulangi dari awal.']);
        }

        $record = $this->findValidToken($request->email, $request->token);

        if (! $record || ! $record->verified) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link tidak valid, belum diverifikasi, atau sudah kedaluwarsa.']);
        }

        return view('auth.reset-password-identity', [
            'email' => $request->email,
            'token' => $request->token,
        ]);
    }

    // Langkah 3: Proses simpan password baru
    public function resetSubmit(Request $request)
    {
        $request->validate([
            'email'                 => ['required', 'email'],
            'token'                 => ['required'],
            'password'              => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $record = $this->findValidToken($request->email, $request->token);

        if (! $record || ! $record->verified) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus token setelah dipakai
        DB::table('identity_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }

    // Helper: cek token valid dan belum kedaluwarsa (30 menit)
    private function findValidToken(string $email, string $token): ?object
    {
        $record = DB::table('identity_reset_tokens')->where('email', $email)->first();

        if (! $record) {
            return null;
        }

        // Cek kedaluwarsa 30 menit
        if (now()->diffInMinutes($record->created_at) > 30) {
            DB::table('identity_reset_tokens')->where('email', $email)->delete();
            return null;
        }

        if (! Hash::check($token, $record->token)) {
            return null;
        }

        return $record;
    }
}

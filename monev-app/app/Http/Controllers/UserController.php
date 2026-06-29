<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dosen;
use App\Models\Kegiatan;
use App\Models\PenugasanReviewer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function create()
    {
        return view('users.create', [
            'roles'       => Role::orderBy('name')->get(),
            'dosenTanpaAkun' => Dosen::whereNull('user_id')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', Password::min(8)],
            'role'      => 'required|exists:roles,name',
            'nip'       => 'nullable|string|max:30|unique:users,nip',
            'nidn'      => 'nullable|string|max:20|unique:users,nidn',
            'dosen_id'  => 'nullable|exists:dosen,id',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'nip'      => $data['nip'] ?? null,
            'nidn'     => $data['nidn'] ?? null,
        ]);

        $user->assignRole($data['role']);

        if (!empty($data['dosen_id'])) {
            Dosen::where('id', $data['dosen_id'])->update(['user_id' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', "User \"{$user->name}\" berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user'           => $user->load('roles', 'dosen'),
            'roles'          => Role::orderBy('name')->get(),
            'dosenTanpaAkun' => Dosen::whereNull('user_id')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'password' => ['nullable', Password::min(8)],
            'role'     => 'required|exists:roles,name',
            'nip'      => "nullable|string|max:30|unique:users,nip,{$user->id}",
            'nidn'     => "nullable|string|max:20|unique:users,nidn,{$user->id}",
            'dosen_id' => 'nullable|exists:dosen,id',
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'nip'   => $data['nip'] ?? null,
            'nidn'  => $data['nidn'] ?? null,
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $user->syncRoles([$data['role']]);

        // Lepas relasi dosen lama milik user ini
        Dosen::where('user_id', $user->id)->update(['user_id' => null]);

        if (!empty($data['dosen_id'])) {
            Dosen::where('id', $data['dosen_id'])->update(['user_id' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Blok jika user memiliki kegiatan yang dibuat (created_by)
        $jumlahKegiatan = Kegiatan::where('created_by', $user->id)->count();
        if ($jumlahKegiatan > 0) {
            return back()->with('error',
                "Tidak bisa menghapus: user ini memiliki {$jumlahKegiatan} kegiatan yang terdaftar."
            );
        }

        // Blok jika user memiliki log status atau berkas (audit trail)
        $jumlahLog = \App\Models\KegiatanStatusLog::where('oleh_user_id', $user->id)->count();
        if ($jumlahLog > 0) {
            return back()->with('error',
                "Tidak bisa menghapus: user ini memiliki {$jumlahLog} log aktivitas dalam sistem."
            );
        }

        $jumlahBerkas = \App\Models\KegiatanBerkas::where('uploaded_by', $user->id)->count();
        if ($jumlahBerkas > 0) {
            return back()->with('error',
                "Tidak bisa menghapus: user ini telah mengunggah {$jumlahBerkas} berkas kegiatan."
            );
        }

        // Hapus penugasan reviewer (aman untuk dihapus, bisa di-assign ulang)
        PenugasanReviewer::where('reviewer_user_id', $user->id)->delete();

        // Putuskan link ke profil dosen
        Dosen::where('user_id', $user->id)->update(['user_id' => null]);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function importForm()
    {
        return view('users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.mimes' => 'File harus berformat CSV.',
            'file.max'   => 'Ukuran file maksimal 2 MB.',
        ]);

        $validRoles = Role::pluck('name')->toArray();
        $handle     = fopen($request->file('file')->getRealPath(), 'r');

        // Skip header row
        $header = fgetcsv($handle);

        $imported = 0;
        $skipped  = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (count($row) < 4) {
                $skipped[] = "Baris {$rowNum}: kolom tidak lengkap.";
                continue;
            }

            [$name, $email, $password, $role] = array_map('trim', array_slice($row, 0, 4));
            $nip  = trim($row[4] ?? '');
            $nidn = trim($row[5] ?? '');

            if (!$name || !$email || !$password || !$role) {
                $skipped[] = "Baris {$rowNum}: kolom name/email/password/role wajib diisi.";
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped[] = "Baris {$rowNum}: format email \"{$email}\" tidak valid.";
                continue;
            }
            if (!in_array($role, $validRoles)) {
                $skipped[] = "Baris {$rowNum}: role \"{$role}\" tidak dikenal. Gunakan: " . implode(', ', $validRoles) . ".";
                continue;
            }
            if (strlen($password) < 8) {
                $skipped[] = "Baris {$rowNum}: password minimal 8 karakter.";
                continue;
            }
            if (User::where('email', $email)->exists()) {
                $skipped[] = "Baris {$rowNum}: email \"{$email}\" sudah terdaftar.";
                continue;
            }
            if ($nip && User::where('nip', $nip)->exists()) {
                $skipped[] = "Baris {$rowNum}: NIP \"{$nip}\" sudah digunakan.";
                continue;
            }
            if ($nidn && User::where('nidn', $nidn)->exists()) {
                $skipped[] = "Baris {$rowNum}: NIDN \"{$nidn}\" sudah digunakan.";
                continue;
            }

            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
                'nip'      => $nip ?: null,
                'nidn'     => $nidn ?: null,
            ]);
            $user->assignRole($role);
            $imported++;
        }

        fclose($handle);

        $msg = "{$imported} user berhasil diimpor.";
        if ($skipped) {
            $msg .= ' ' . count($skipped) . ' baris dilewati.';
            return redirect()->route('users.import')
                ->with('import_success', $msg)
                ->with('import_skipped', $skipped);
        }

        return redirect()->route('users.index')
            ->with('success', $msg);
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        $rows = [
            ['name', 'email', 'password', 'role', 'nip', 'nidn'],
            ['Budi Santoso', 'budi@example.com', 'password123', 'dosen', '198501012010011001', '0101085001'],
            ['Siti Rahayu', 'siti@example.com', 'password123', 'reviewer', '', '0102086002'],
            ['Admin P3KM', 'admin2@example.com', 'password123', 'admin', '', ''],
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}

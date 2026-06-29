<?php
namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        return view('dosen.index');
    }

    public function create()
    {
        return view('dosen.create', [
            'fakultasAll'  => Fakultas::with('prodi')->orderBy('nama')->get(),
            'usersAvailable' => User::whereDoesntHave('dosen')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'nidn'     => 'required|string|max:20|unique:dosen,nidn',
            'email'    => 'nullable|email|max:255',
            'no_hp'    => 'nullable|string|max:20',
            'prodi_id' => 'required|exists:prodi,id',
            'user_id'  => 'nullable|exists:users,id|unique:dosen,user_id',
        ], [
            'nidn.unique'    => 'NIDN sudah terdaftar.',
            'user_id.unique' => 'Akun user ini sudah terhubung ke dosen lain.',
        ]);

        Dosen::create($data);

        return redirect()->route('dosen.index')
            ->with('success', "Dosen \"{$data['nama']}\" berhasil ditambahkan.");
    }

    public function edit(Dosen $dosen)
    {
        return view('dosen.edit', [
            'dosen'          => $dosen->load('prodi.fakultas', 'user'),
            'fakultasAll'    => Fakultas::with('prodi')->orderBy('nama')->get(),
            'usersAvailable' => User::whereDoesntHave('dosen')
                                    ->orWhere('id', $dosen->user_id)
                                    ->orderBy('name')
                                    ->get(),
        ]);
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:255',
            'nidn'     => "required|string|max:20|unique:dosen,nidn,{$dosen->id}",
            'email'    => 'nullable|email|max:255',
            'no_hp'    => 'nullable|string|max:20',
            'prodi_id' => 'required|exists:prodi,id',
            'user_id'  => "nullable|exists:users,id|unique:dosen,user_id,{$dosen->id}",
        ], [
            'nidn.unique'    => 'NIDN sudah digunakan dosen lain.',
            'user_id.unique' => 'Akun user ini sudah terhubung ke dosen lain.',
        ]);

        $dosen->update($data);

        return redirect()->route('dosen.index')
            ->with('success', "Data dosen \"{$dosen->nama}\" berhasil diperbarui.");
    }

    public function destroy(Dosen $dosen)
    {
        if ($dosen->kegiatan()->exists()) {
            return back()->with('error', "Tidak bisa menghapus dosen yang masih memiliki kegiatan.");
        }

        $nama = $dosen->nama;
        $dosen->delete();

        return redirect()->route('dosen.index')
            ->with('success', "Dosen \"{$nama}\" berhasil dihapus.");
    }

    public function importForm()
    {
        $prodiAll = Prodi::with('fakultas')->orderBy('nama')->get();
        return view('dosen.import', compact('prodiAll'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.mimes' => 'File harus berformat CSV.',
            'file.max'   => 'Ukuran file maksimal 2 MB.',
        ]);

        // Preload prodi map: lowercase(nama) => id
        $prodiMap = Prodi::all()->keyBy(fn($p) => strtolower(trim($p->nama)));

        $handle   = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($handle); // skip header

        $imported = 0;
        $skipped  = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Abaikan baris komentar dari template
            if (isset($row[0]) && str_starts_with(trim($row[0]), '#')) {
                continue;
            }

            if (count($row) < 3) {
                $skipped[] = "Baris {$rowNum}: kolom tidak lengkap (minimal: nama, nidn, prodi).";
                continue;
            }

            $nama  = trim($row[0] ?? '');
            $nidn  = trim($row[1] ?? '');
            $prodi = trim($row[2] ?? '');
            $email = trim($row[3] ?? '');
            $noHp  = trim($row[4] ?? '');
            $userEmail = trim($row[5] ?? '');

            if (!$nama || !$nidn || !$prodi) {
                $skipped[] = "Baris {$rowNum}: kolom nama/nidn/prodi wajib diisi.";
                continue;
            }
            if (strlen($nidn) > 20) {
                $skipped[] = "Baris {$rowNum}: NIDN \"{$nidn}\" melebihi 20 karakter.";
                continue;
            }
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped[] = "Baris {$rowNum}: format email \"{$email}\" tidak valid.";
                continue;
            }
            if (Dosen::where('nidn', $nidn)->exists()) {
                $skipped[] = "Baris {$rowNum}: NIDN \"{$nidn}\" sudah terdaftar.";
                continue;
            }

            $prodiObj = $prodiMap[strtolower($prodi)] ?? null;
            if (!$prodiObj) {
                $skipped[] = "Baris {$rowNum}: Program studi \"{$prodi}\" tidak ditemukan di sistem.";
                continue;
            }

            $userId = null;
            if ($userEmail) {
                $user = User::where('email', $userEmail)->first();
                if (!$user) {
                    $skipped[] = "Baris {$rowNum}: User dengan email \"{$userEmail}\" tidak ditemukan — dosen tetap diimpor tanpa akun.";
                } elseif (Dosen::where('user_id', $user->id)->exists()) {
                    $skipped[] = "Baris {$rowNum}: Akun \"{$userEmail}\" sudah terhubung ke dosen lain — dosen tetap diimpor tanpa akun.";
                } else {
                    $userId = $user->id;
                }
            }

            Dosen::create([
                'nama'     => $nama,
                'nidn'     => $nidn,
                'prodi_id' => $prodiObj->id,
                'email'    => $email ?: null,
                'no_hp'    => $noHp ?: null,
                'user_id'  => $userId,
            ]);
            $imported++;
        }

        fclose($handle);

        $msg = "{$imported} dosen berhasil diimpor.";
        if ($skipped) {
            $msg .= ' ' . count($skipped) . ' baris dilewati.';
            return redirect()->route('dosen.import')
                ->with('import_success', $msg)
                ->with('import_skipped', $skipped);
        }

        return redirect()->route('dosen.index')
            ->with('success', $msg);
    }

    public function importTemplate()
    {
        $prodiAll = Prodi::with('fakultas')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_dosen.csv"',
        ];

        $callback = function () use ($prodiAll) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['nama', 'nidn', 'prodi', 'email', 'no_hp', 'user_email']);

            // Contoh baris
            $firstProdi = $prodiAll->first();
            fputcsv($out, [
                'Prof. Dr. Budi Santoso, M.T.',
                '0101018001',
                $firstProdi?->nama ?? 'Teknik Informatika',
                'budi@kampus.ac.id',
                '081234567890',
                '',
            ]);
            fputcsv($out, [
                'Dr. Siti Rahayu, M.Kom.',
                '0202028002',
                $firstProdi?->nama ?? 'Teknik Informatika',
                'siti@kampus.ac.id',
                '',
                '',
            ]);

            // Komentar daftar prodi yang tersedia
            fputcsv($out, []);
            fputcsv($out, ['# Daftar nama prodi yang tersedia di sistem:']);
            foreach ($prodiAll as $p) {
                fputcsv($out, ["# {$p->nama} ({$p->fakultas?->nama})"]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}

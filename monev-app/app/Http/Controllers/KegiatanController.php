<?php
namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Kategori;
use App\Models\Skema;
use App\Models\Dosen;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        return view('kegiatan.index');
    }

    public function create()
    {
        return view('kegiatan.create', [
            'kategori' => Kategori::orderBy('nama')->get(),
            'skema'    => Skema::where('aktif', 1)->with('kategori')->orderBy('nama')->get(),
            'dosen'    => Dosen::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'          => 'required|string|max:500',
            'kategori_id'    => 'required|exists:kategori,id',
            'skema_id'       => 'required|exists:skema,id',
            'ketua_dosen_id' => 'required|exists:dosen,id',
            'tahun'          => 'required|integer|min:2000|max:2099',
            'sumber_dana'    => 'nullable|string|max:255',
            'jumlah_dana'    => 'nullable|numeric|min:0',
            'tanggal_mulai'  => 'nullable|date',
            'tanggal_selesai'=> 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan_admin'  => 'nullable|string',
        ]);

        $data['status']     = 'TERDAFTAR';
        $data['created_by'] = auth()->id();

        Kegiatan::create($data);

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan)
    {
        return view('kegiatan.edit', [
            'kegiatan' => $kegiatan,
            'kategori' => Kategori::orderBy('nama')->get(),
            'skema'    => Skema::where('aktif', 1)->with('kategori')->orderBy('nama')->get(),
            'dosen'    => Dosen::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $data = $request->validate([
            'judul'          => 'required|string|max:500',
            'kategori_id'    => 'required|exists:kategori,id',
            'skema_id'       => 'required|exists:skema,id',
            'ketua_dosen_id' => 'required|exists:dosen,id',
            'tahun'          => 'required|integer|min:2000|max:2099',
            'sumber_dana'    => 'nullable|string|max:255',
            'jumlah_dana'    => 'nullable|numeric|min:0',
            'tanggal_mulai'  => 'nullable|date',
            'tanggal_selesai'=> 'nullable|date|after_or_equal:tanggal_mulai',
            'status'         => 'required|in:TERDAFTAR,BERJALAN,LAPORAN_MASUK,DINILAI,SELESAI',
            'catatan_admin'  => 'nullable|string',
        ]);

        $kegiatan->update($data);

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function importForm()
    {
        $skemaAll    = Skema::with('kategori')->orderBy('nama')->get();
        $kategoriAll = Kategori::orderBy('nama')->get();
        return view('kegiatan.import', compact('skemaAll', 'kategoriAll'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.mimes' => 'File harus berformat CSV.',
            'file.max'   => 'Ukuran file maksimal 2 MB.',
        ]);

        // Preload lookup maps (case-insensitive by nama)
        $skemaMap    = Skema::all()->keyBy(fn($s) => strtolower(trim($s->nama)));
        $kategoriMap = Kategori::all()->keyBy(fn($k) => strtolower(trim($k->nama)));
        $kategoriByKode = Kategori::all()->keyBy(fn($k) => strtoupper(trim($k->kode)));
        $dosenMap    = Dosen::all()->keyBy(fn($d) => trim($d->nidn));

        $validStatuses = ['TERDAFTAR', 'BERJALAN', 'LAPORAN_MASUK', 'DINILAI', 'SELESAI'];

        $handle  = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($handle); // skip header

        $imported = 0;
        $skipped  = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (isset($row[0]) && str_starts_with(trim($row[0]), '#')) {
                continue;
            }

            if (count($row) < 4) {
                $skipped[] = "Baris {$rowNum}: kolom tidak lengkap (minimal: judul, skema, ketua_nidn, tahun).";
                continue;
            }

            $judul        = trim($row[0] ?? '');
            $skemaNama    = trim($row[1] ?? '');
            $ketuaNidn    = trim($row[2] ?? '');
            $tahun        = trim($row[3] ?? '');
            $kategoriNama = trim($row[4] ?? '');
            $sumberDana   = trim($row[5] ?? '');
            $jumlahDana   = trim($row[6] ?? '');
            $tglMulai     = trim($row[7] ?? '');
            $tglSelesai   = trim($row[8] ?? '');
            $status       = strtoupper(trim($row[9] ?? 'TERDAFTAR'));
            $catatan      = trim($row[10] ?? '');

            if (!$judul || !$skemaNama || !$ketuaNidn || !$tahun) {
                $skipped[] = "Baris {$rowNum}: kolom judul/skema/ketua_nidn/tahun wajib diisi.";
                continue;
            }

            if (!is_numeric($tahun) || (int)$tahun < 2000 || (int)$tahun > 2099) {
                $skipped[] = "Baris {$rowNum}: tahun \"{$tahun}\" tidak valid (harus 2000–2099).";
                continue;
            }

            $skemaObj = $skemaMap[strtolower($skemaNama)] ?? null;
            if (!$skemaObj) {
                $skipped[] = "Baris {$rowNum}: Skema \"{$skemaNama}\" tidak ditemukan di sistem.";
                continue;
            }

            $dosenObj = $dosenMap[$ketuaNidn] ?? null;
            if (!$dosenObj) {
                $skipped[] = "Baris {$rowNum}: Dosen dengan NIDN \"{$ketuaNidn}\" tidak ditemukan.";
                continue;
            }

            // Kategori: gunakan dari kolom jika ada, fallback ke kategori skema
            if ($kategoriNama) {
                $kategoriObj = $kategoriMap[strtolower($kategoriNama)]
                            ?? $kategoriByKode[strtoupper($kategoriNama)]
                            ?? null;
                if (!$kategoriObj) {
                    $skipped[] = "Baris {$rowNum}: Kategori \"{$kategoriNama}\" tidak ditemukan.";
                    continue;
                }
            } else {
                $kategoriObj = $skemaObj->kategori;
                if (!$kategoriObj) {
                    $skipped[] = "Baris {$rowNum}: Skema tidak memiliki kategori default — isi kolom kategori.";
                    continue;
                }
            }

            if ($status && !in_array($status, $validStatuses)) {
                $skipped[] = "Baris {$rowNum}: status \"{$status}\" tidak valid. Gunakan: " . implode(', ', $validStatuses) . ".";
                continue;
            }

            if ($jumlahDana !== '' && !is_numeric($jumlahDana)) {
                $skipped[] = "Baris {$rowNum}: jumlah_dana \"{$jumlahDana}\" harus berupa angka.";
                continue;
            }

            if ($tglMulai && !strtotime($tglMulai)) {
                $skipped[] = "Baris {$rowNum}: tanggal_mulai \"{$tglMulai}\" tidak valid (gunakan format YYYY-MM-DD).";
                continue;
            }
            if ($tglSelesai && !strtotime($tglSelesai)) {
                $skipped[] = "Baris {$rowNum}: tanggal_selesai \"{$tglSelesai}\" tidak valid (gunakan format YYYY-MM-DD).";
                continue;
            }

            Kegiatan::create([
                'judul'          => $judul,
                'skema_id'       => $skemaObj->id,
                'kategori_id'    => $kategoriObj->id,
                'ketua_dosen_id' => $dosenObj->id,
                'tahun'          => (int)$tahun,
                'sumber_dana'    => $sumberDana ?: null,
                'jumlah_dana'    => $jumlahDana !== '' ? (float)$jumlahDana : 0,
                'tanggal_mulai'  => $tglMulai ?: null,
                'tanggal_selesai'=> $tglSelesai ?: null,
                'status'         => $status ?: 'TERDAFTAR',
                'catatan_admin'  => $catatan ?: null,
                'created_by'     => auth()->id(),
            ]);
            $imported++;
        }

        fclose($handle);

        $msg = "{$imported} kegiatan berhasil diimpor.";
        if ($skipped) {
            $msg .= ' ' . count($skipped) . ' baris dilewati.';
            return redirect()->route('kegiatan.import')
                ->with('import_success', $msg)
                ->with('import_skipped', $skipped);
        }

        return redirect()->route('kegiatan.index')
            ->with('success', $msg);
    }

    public function importTemplate()
    {
        $skemaAll = Skema::with('kategori')->get();
        $dosenAll = Dosen::orderBy('nama')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_kegiatan.csv"',
        ];

        $callback = function () use ($skemaAll, $dosenAll) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['judul', 'skema', 'ketua_nidn', 'tahun', 'kategori', 'sumber_dana', 'jumlah_dana', 'tanggal_mulai', 'tanggal_selesai', 'status', 'catatan_admin']);

            $firstSkema = $skemaAll->first();
            $firstDosen = $dosenAll->first();
            fputcsv($out, [
                'Penelitian Pemanfaatan AI dalam Pembelajaran',
                $firstSkema?->nama ?? 'Penelitian Dasar',
                $firstDosen?->nidn ?? '0001018001',
                date('Y'),
                '',
                'DIPA',
                '15000000',
                date('Y') . '-01-15',
                date('Y') . '-12-15',
                'TERDAFTAR',
                '',
            ]);
            fputcsv($out, [
                'PKM Pelatihan Digital Marketing UMKM Desa',
                $skemaAll->skip(3)->first()?->nama ?? 'PKM Kemitraan Masyarakat',
                $dosenAll->skip(1)->first()?->nidn ?? '0002028002',
                date('Y'),
                '',
                'DIPA',
                '20000000',
                date('Y') . '-03-01',
                date('Y') . '-11-30',
                'TERDAFTAR',
                '',
            ]);

            fputcsv($out, []);
            fputcsv($out, ['# Daftar skema yang tersedia:']);
            foreach ($skemaAll as $s) {
                fputcsv($out, ["# {$s->nama} (kategori: {$s->kategori?->nama})"]);
            }
            fputcsv($out, []);
            fputcsv($out, ['# Status yang valid: TERDAFTAR, BERJALAN, LAPORAN_MASUK, DINILAI, SELESAI']);
            fputcsv($out, ['# Format tanggal: YYYY-MM-DD (contoh: 2026-01-15)']);
            fputcsv($out, ['# Kolom kategori boleh dikosongkan — akan otomatis diambil dari skema']);
            fputcsv($out, []);
            fputcsv($out, ['# Contoh NIDN dosen yang terdaftar:']);
            foreach ($dosenAll->take(10) as $d) {
                fputcsv($out, ["# {$d->nidn} — {$d->nama}"]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function trash()
    {
        $kegiatan = Kegiatan::onlyTrashed()
            ->with(['skema', 'kategori', 'ketua'])
            ->latest('deleted_at')
            ->paginate(15);
        return view('kegiatan.trash', compact('kegiatan'));
    }

    public function restore(int $id)
    {
        Kegiatan::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('kegiatan.trash')->with('success', 'Kegiatan berhasil dipulihkan.');
    }

    public function forceDelete(int $id)
    {
        $kegiatan = Kegiatan::onlyTrashed()->findOrFail($id);
        $kegiatan->forceDelete();
        return redirect()->route('kegiatan.trash')->with('success', 'Kegiatan dihapus permanen.');
    }
}

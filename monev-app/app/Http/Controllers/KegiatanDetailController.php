<?php
namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kegiatan;
use App\Models\KegiatanBerkas;
use App\Models\KegiatanLuaran;
use App\Models\KegiatanStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanDetailController extends Controller
{
    public function show(Kegiatan $kegiatan)
    {
        $kegiatan->load([
            'skema', 'kategori', 'ketua.prodi.fakultas',
            'anggota.prodi',
            'penugasanReviewer.reviewer',
            'penugasanReviewer.penilaian',
            'luaran',
            'berkas.uploadedBy',
            'statusLog.oleh',
        ]);

        $dosenAvailable = Dosen::whereNotIn('id',
            $kegiatan->anggota->pluck('id')->push($kegiatan->ketua_dosen_id)->filter()
        )->orderBy('nama')->get();

        return view('kegiatan.show', compact('kegiatan', 'dosenAvailable'));
    }

    public function ubahStatus(Request $request, Kegiatan $kegiatan)
    {
        $allowed = $kegiatan->nextStatuses();

        $data = $request->validate([
            'status_baru' => ['required', 'in:' . implode(',', $allowed)],
            'catatan'     => 'nullable|string|max:1000',
        ]);

        KegiatanStatusLog::create([
            'kegiatan_id'  => $kegiatan->id,
            'status_lama'  => $kegiatan->status,
            'status_baru'  => $data['status_baru'],
            'oleh_user_id' => auth()->id(),
            'catatan'      => $data['catatan'] ?? null,
            'created_at'   => now(),
        ]);

        $kegiatan->update(['status' => $data['status_baru']]);

        if ($data['status_baru'] === 'SELESAI') {
            $kegiatan->hitungSkorFinal();
        }

        return back()->with('success', "Status kegiatan diubah ke \"{$kegiatan->fresh()->statusLabel()}\".");
    }

    public function tambahAnggota(Request $request, Kegiatan $kegiatan)
    {
        $data = $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'peran'    => 'nullable|string|max:100',
        ]);

        if ($kegiatan->anggota()->where('dosen_id', $data['dosen_id'])->exists()) {
            return back()->with('error', 'Dosen sudah menjadi anggota kegiatan ini.');
        }
        if ($kegiatan->ketua_dosen_id == $data['dosen_id']) {
            return back()->with('error', 'Ketua tidak bisa ditambahkan sebagai anggota.');
        }

        $kegiatan->anggota()->attach($data['dosen_id'], ['peran' => $data['peran'] ?: 'Anggota']);
        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function hapusAnggota(Kegiatan $kegiatan, Dosen $dosen)
    {
        $kegiatan->anggota()->detach($dosen->id);
        return back()->with('success', 'Anggota dihapus dari kegiatan.');
    }

    public function uploadBerkas(Request $request, Kegiatan $kegiatan)
    {
        $data = $request->validate([
            'jenis' => 'required|in:LAPORAN_KEMAJUAN,LAPORAN_AKHIR,BUKTI_LUARAN,LAMPIRAN',
            'file'  => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,zip,jpg,png',
        ]);

        $file      = $request->file('file');
        $path      = $file->store("kegiatan/{$kegiatan->id}/berkas", 'public');
        $namaFile  = $file->getClientOriginalName();
        $ukuran    = $file->getSize();

        KegiatanBerkas::create([
            'kegiatan_id' => $kegiatan->id,
            'jenis'       => $data['jenis'],
            'nama_file'   => $namaFile,
            'path'        => $path,
            'ukuran_byte' => $ukuran,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Berkas berhasil diunggah.');
    }

    public function hapusBerkas(Kegiatan $kegiatan, KegiatanBerkas $berkas)
    {
        Storage::disk('public')->delete($berkas->path);
        $berkas->delete();
        return back()->with('success', 'Berkas dihapus.');
    }

    public function tambahLuaran(Request $request, Kegiatan $kegiatan)
    {
        $data = $request->validate([
            'jenis'          => 'required|in:PUBLIKASI,HKI,PRODUK,LAPORAN,LAINNYA',
            'judul_luaran'   => 'required|string|max:500',
            'status_capaian' => 'required|in:RENCANA,PROSES,TERCAPAI',
            'url_bukti'      => 'nullable|url|max:500',
            'keterangan'     => 'nullable|string|max:1000',
        ]);

        $kegiatan->luaran()->create($data);
        return back()->with('success', 'Luaran berhasil ditambahkan.');
    }

    public function hapusLuaran(Kegiatan $kegiatan, KegiatanLuaran $luaran)
    {
        $luaran->delete();
        return back()->with('success', 'Luaran dihapus.');
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kegiatan;
use App\Models\KriteriaPenilaian;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PenugasanReviewer;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    // ── Reviewer: Daftar Tugas ───────────────────────────────────────────────
    public function tugas()
    {
        $penugasan = PenugasanReviewer::with(['kegiatan.skema', 'kegiatan.kategori', 'kegiatan.ketua', 'penilaian'])
            ->where('reviewer_user_id', auth()->id())
            ->orderByRaw("CASE status WHEN 'MENUNGGU' THEN 0 WHEN 'DALAM_PENILAIAN' THEN 1 ELSE 2 END")
            ->get();

        return view('reviewer.tugas', compact('penugasan'));
    }

    // ── Reviewer: Form Penilaian ─────────────────────────────────────────────
    public function form(PenugasanReviewer $penugasan)
    {
        abort_unless($penugasan->reviewer_user_id === auth()->id(), 403);

        $kegiatan = $penugasan->kegiatan()->with(['skema', 'kategori', 'ketua'])->firstOrFail();

        $kriteria = KriteriaPenilaian::where('aktif', true)
            ->where(fn($q) =>
                $q->where('scope', 'GLOBAL')
                  ->orWhere(fn($q) => $q->where('scope', 'KATEGORI')->where('kategori_id', $kegiatan->kategori_id))
                  ->orWhere(fn($q) => $q->where('scope', 'SKEMA')->where('skema_id', $kegiatan->skema_id))
            )
            ->orderBy('urutan')
            ->get();

        $penilaian = $penugasan->penilaian ?? new Penilaian(['status' => 'DRAFT']);
        $detailMap = $penilaian->detail()->pluck('skor', 'kriteria_id');

        return view('reviewer.penilaian', compact('penugasan', 'kegiatan', 'kriteria', 'penilaian', 'detailMap'));
    }

    public function simpan(Request $request, PenugasanReviewer $penugasan)
    {
        abort_unless($penugasan->reviewer_user_id === auth()->id(), 403);

        $kegiatan = $penugasan->kegiatan()->with(['skema', 'kategori'])->firstOrFail();

        $kriteria = KriteriaPenilaian::where('aktif', true)
            ->where(fn($q) =>
                $q->where('scope', 'GLOBAL')
                  ->orWhere(fn($q) => $q->where('scope', 'KATEGORI')->where('kategori_id', $kegiatan->kategori_id))
                  ->orWhere(fn($q) => $q->where('scope', 'SKEMA')->where('skema_id', $kegiatan->skema_id))
            )
            ->get();

        // Validasi skor per kriteria
        $rules = ['rekomendasi' => 'required|in:LANJUT,PERBAIKAN,DIHENTIKAN', 'catatan' => 'nullable|string|max:2000'];
        foreach ($kriteria as $k) {
            $rules["skor.{$k->id}"] = "required|numeric|min:{$k->skor_min}|max:{$k->skor_max}";
        }
        $data = $request->validate($rules);

        // Hitung skor_akhir = weighted average (bobot / 100 * skor)
        $skorAkhir = 0;
        $totalBobot = $kriteria->sum('bobot');
        foreach ($kriteria as $k) {
            $skor = (float) $data['skor'][$k->id];
            $skorAkhir += ($k->bobot / ($totalBobot ?: 100)) * $skor;
        }

        $isFinal   = $request->has('submit_final');
        $statusPen = $isFinal ? 'FINAL' : 'DRAFT';

        $penilaian = Penilaian::updateOrCreate(
            ['penugasan_id' => $penugasan->id],
            [
                'skor_akhir'   => round($skorAkhir, 2),
                'rekomendasi'  => $data['rekomendasi'],
                'catatan'      => $data['catatan'] ?? null,
                'status'       => $statusPen,
                'dinilai_at'   => $isFinal ? now() : null,
            ]
        );

        foreach ($kriteria as $k) {
            PenilaianDetail::updateOrCreate(
                ['penilaian_id' => $penilaian->id, 'kriteria_id' => $k->id],
                ['skor' => (float) $data['skor'][$k->id]]
            );
        }

        // Update status penugasan
        $penugasan->update(['status' => $isFinal ? 'SELESAI' : 'DALAM_PENILAIAN']);

        // Jika semua reviewer final, hitung skor kegiatan
        if ($isFinal) {
            $kegiatan->fresh()->hitungSkorFinal();
        }

        $msg = $isFinal ? 'Penilaian berhasil disubmit.' : 'Draft penilaian tersimpan.';
        return redirect()->route('tugas.index')->with('success', $msg);
    }

    // ── Dosen: Kegiatan Saya ─────────────────────────────────────────────────
    public function kegiatanSaya()
    {
        $dosen = auth()->user()->dosen;

        if (!$dosen) {
            return view('dosen.kegiatan-saya', ['sebagaiKetua' => collect(), 'sebagaiAnggota' => collect(), 'dosenAda' => false]);
        }

        $sebagaiKetua = Kegiatan::with(['skema', 'kategori', 'penugasanReviewer.penilaian'])
            ->where('ketua_dosen_id', $dosen->id)
            ->orderByDesc('tahun')->orderByDesc('created_at')
            ->get();

        $sebagaiAnggota = Dosen::find($dosen->id)
            ->kegiatanAnggota()
            ->with(['skema', 'kategori', 'ketua'])
            ->orderByDesc('tahun')
            ->get();

        return view('dosen.kegiatan-saya', compact('sebagaiKetua', 'sebagaiAnggota') + ['dosenAda' => true]);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kegiatan;
use App\Models\Skema;
use App\Models\PenugasanReviewer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $role  = $user->getRoleNames()->first() ?? '';
        $tahun = request('tahun', date('Y'));

        if ($role === 'reviewer') {
            return $this->reviewerDashboard($user, $tahun);
        }
        if ($role === 'dosen') {
            return $this->dosenDashboard($user, $tahun);
        }

        return $this->adminDashboard($tahun);
    }

    private function reviewerDashboard($user, $tahun)
    {
        $tugas = PenugasanReviewer::with(['kegiatan.skema', 'kegiatan.ketua', 'penilaian'])
            ->where('reviewer_user_id', $user->id)
            ->whereHas('kegiatan', fn($q) => $q->where('tahun', $tahun))
            ->orderByRaw("CASE status WHEN 'MENUNGGU' THEN 0 WHEN 'DALAM_PENILAIAN' THEN 1 ELSE 2 END")
            ->get();

        $tahunList = PenugasanReviewer::where('reviewer_user_id', $user->id)
            ->join('kegiatan', 'penugasan_reviewer.kegiatan_id', '=', 'kegiatan.id')
            ->selectRaw('DISTINCT kegiatan.tahun')
            ->orderByDesc('kegiatan.tahun')
            ->pluck('kegiatan.tahun');

        return view('dashboard-reviewer', [
            'tugas'      => $tugas,
            'total'      => $tugas->count(),
            'menunggu'   => $tugas->where('status', 'MENUNGGU')->count(),
            'dalamProses'=> $tugas->where('status', 'DALAM_PENILAIAN')->count(),
            'selesai'    => $tugas->where('status', 'SELESAI')->count(),
            'tahun'      => $tahun,
            'tahunList'  => $tahunList,
        ]);
    }

    private function dosenDashboard($user, $tahun)
    {
        $dosen = Dosen::where('user_id', $user->id)->first();

        $sebagaiKetua = $dosen
            ? Kegiatan::with(['skema', 'kategori'])
                ->where('ketua_dosen_id', $dosen->id)->where('tahun', $tahun)->get()
            : collect();

        $sebagaiAnggota = $dosen
            ? Kegiatan::with(['skema', 'kategori', 'ketua'])
                ->whereHas('anggota', fn($q) => $q->where('dosen_id', $dosen->id))
                ->where('tahun', $tahun)->get()
            : collect();

        $tahunList = $dosen
            ? Kegiatan::where(function ($q) use ($dosen) {
                $q->where('ketua_dosen_id', $dosen->id)
                  ->orWhereHas('anggota', fn($q2) => $q2->where('dosen_id', $dosen->id));
            })->selectRaw('DISTINCT tahun')->orderByDesc('tahun')->pluck('tahun')
            : collect();

        return view('dashboard-dosen', compact(
            'dosen', 'sebagaiKetua', 'sebagaiAnggota', 'tahun', 'tahunList'
        ));
    }

    private function adminDashboard($tahun)
    {
        // Stat cards — jumlah per status
        $stats = Kegiatan::query()
            ->when($tahun, fn($q) => $q->where('tahun', $tahun))
            ->selectRaw("
                SUM(CASE WHEN status = 'TERDAFTAR'     THEN 1 ELSE 0 END) as terdaftar,
                SUM(CASE WHEN status = 'BERJALAN'      THEN 1 ELSE 0 END) as berjalan,
                SUM(CASE WHEN status = 'LAPORAN_MASUK' THEN 1 ELSE 0 END) as laporan_masuk,
                SUM(CASE WHEN status = 'DINILAI'       THEN 1 ELSE 0 END) as dinilai,
                SUM(CASE WHEN status = 'SELESAI'       THEN 1 ELSE 0 END) as selesai
            ")
            ->first();

        // Distribusi per skema — pakai join agar kompatibel dengan SQLite
        $distribusiSkema = DB::table('skema')
            ->join('kategori', 'skema.kategori_id', '=', 'kategori.id')
            ->leftJoin('kegiatan', function ($join) use ($tahun) {
                $join->on('skema.id', '=', 'kegiatan.skema_id')
                     ->where('kegiatan.tahun', '=', $tahun)
                     ->whereNull('kegiatan.deleted_at');
            })
            ->select('skema.id', 'skema.nama', 'kategori.kode as kategori_kode',
                     DB::raw('COUNT(kegiatan.id) as jumlah'))
            ->where('skema.aktif', 1)
            ->groupBy('skema.id', 'skema.nama', 'kategori.kode')
            ->having(DB::raw('COUNT(kegiatan.id)'), '>', 0)
            ->orderByDesc('jumlah')
            ->get();

        $maxSkema = $distribusiSkema->max('jumlah') ?: 1;

        // Kegiatan terlambat — status BERJALAN/TERDAFTAR lewat tanggal_selesai
        $terlambat = Kegiatan::with(['skema', 'ketua'])
            ->whereIn('status', ['TERDAFTAR', 'BERJALAN'])
            ->where('tanggal_selesai', '<', now())
            ->where('tahun', $tahun)
            ->orderBy('tanggal_selesai')
            ->limit(5)
            ->get();

        // Beban reviewer aktif
        $bebanReviewer = PenugasanReviewer::query()
            ->select('reviewer_user_id', DB::raw('COUNT(*) as jumlah_tugas'))
            ->with('reviewer:id,name')
            ->whereHas('kegiatan', fn($q) => $q->where('tahun', $tahun))
            ->whereIn('status', ['MENUNGGU', 'DALAM_PENILAIAN'])
            ->groupBy('reviewer_user_id')
            ->orderByDesc('jumlah_tugas')
            ->limit(5)
            ->get();

        // Kegiatan terbaru
        $kegiatanTerbaru = Kegiatan::with(['skema', 'kategori', 'ketua'])
            ->where('tahun', $tahun)
            ->latest()
            ->limit(6)
            ->get();

        // Daftar tahun untuk filter
        $tahunList = Kegiatan::selectRaw('DISTINCT tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('dashboard', compact(
            'stats', 'distribusiSkema', 'maxSkema',
            'terlambat', 'bebanReviewer', 'kegiatanTerbaru',
            'tahun', 'tahunList'
        ));
    }
}


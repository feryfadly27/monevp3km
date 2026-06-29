<?php
namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Skema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $tahun  = $request->get('tahun', date('Y'));
        $ketua  = $request->get('ketua', '');
        $skema  = $request->get('skema', '');
        $status = $request->get('status', '');

        $query = Kegiatan::with(['skema', 'kategori', 'ketua', 'penugasanReviewer.penilaian'])
            ->when($tahun,  fn($q) => $q->where('tahun', $tahun))
            ->when($skema,  fn($q) => $q->where('skema_id', $skema))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($ketua,  fn($q) => $q->whereHas('ketua', fn($q2) => $q2->where('nama', 'like', "%{$ketua}%")))
            ->orderBy('status')->orderByDesc('skor_final');

        $kegiatan = $query->get();

        // Agregat untuk summary cards
        $total       = $kegiatan->count();
        $selesai     = $kegiatan->where('status', 'SELESAI')->count();
        $lanjut      = $kegiatan->where('rekomendasi_final', 'LANJUT')->count();
        $dihentikan  = $kegiatan->where('rekomendasi_final', 'DIHENTIKAN')->count();
        $rataaSkor   = $kegiatan->whereNotNull('skor_final')->avg('skor_final');
        $totalDana   = $kegiatan->sum('jumlah_dana');

        // Distribusi per skema
        $perSkema = $kegiatan->groupBy('skema_id')->map(fn($g) => [
            'nama'  => $g->first()->skema?->nama ?? '—',
            'jumlah'=> $g->count(),
            'selesai'=> $g->where('status','SELESAI')->count(),
        ])->sortByDesc('jumlah')->values();

        $tahunList = Kegiatan::selectRaw('DISTINCT tahun')->orderByDesc('tahun')->pluck('tahun');
        $skemaAll  = Skema::orderBy('nama')->get();

        return view('rekap.index', compact(
            'kegiatan', 'total', 'selesai', 'lanjut', 'dihentikan',
            'rataaSkor', 'totalDana', 'perSkema',
            'tahun', 'skema', 'status', 'ketua', 'tahunList', 'skemaAll'
        ));
    }

    public function export(Request $request)
    {
        $tahun  = $request->get('tahun', date('Y'));
        $ketua  = $request->get('ketua', '');
        $skema  = $request->get('skema', '');
        $status = $request->get('status', '');

        $kegiatan = Kegiatan::with(['skema', 'kategori', 'ketua.prodi.fakultas',
                                    'penugasanReviewer.reviewer',
                                    'penugasanReviewer.penilaian'])
            ->when($tahun,  fn($q) => $q->where('tahun', $tahun))
            ->when($skema,  fn($q) => $q->where('skema_id', $skema))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($ketua,  fn($q) => $q->whereHas('ketua', fn($q2) => $q2->where('nama', 'like', "%{$ketua}%")))
            ->orderBy('status')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"rekap_kegiatan_{$tahun}.csv\"",
        ];

        $callback = function () use ($kegiatan) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'No', 'Judul', 'Skema', 'Kategori', 'Tahun',
                'Ketua', 'NIDN Ketua', 'Prodi', 'Fakultas',
                'Sumber Dana', 'Jumlah Dana',
                'Tanggal Mulai', 'Tanggal Selesai',
                'Status', 'Skor Final', 'Rekomendasi Final',
                'Jumlah Reviewer', 'Reviewer',
            ]);

            foreach ($kegiatan as $i => $k) {
                $reviewerNames = $k->penugasanReviewer
                    ->pluck('reviewer.name')->filter()->implode('; ');
                fputcsv($out, [
                    $i + 1,
                    $k->judul,
                    $k->skema?->nama,
                    $k->kategori?->nama,
                    $k->tahun,
                    $k->ketua?->nama,
                    $k->ketua?->nidn,
                    $k->ketua?->prodi?->nama,
                    $k->ketua?->prodi?->fakultas?->nama,
                    $k->sumber_dana,
                    $k->jumlah_dana,
                    $k->tanggal_mulai?->format('Y-m-d'),
                    $k->tanggal_selesai?->format('Y-m-d'),
                    $k->status,
                    $k->skor_final,
                    $k->rekomendasi_final,
                    $k->penugasanReviewer->count(),
                    $reviewerNames,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use SoftDeletes;

    protected $table    = 'kegiatan';
    protected $fillable = [
        'judul', 'skema_id', 'kategori_id', 'tahun', 'ketua_dosen_id',
        'sumber_dana', 'jumlah_dana', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'skor_final', 'rekomendasi_final', 'catatan_admin', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_dana'     => 'float',
        'skor_final'      => 'float',
    ];

    public function skema()             { return $this->belongsTo(Skema::class); }
    public function kategori()          { return $this->belongsTo(Kategori::class); }
    public function ketua()             { return $this->belongsTo(Dosen::class, 'ketua_dosen_id'); }
    public function createdBy()         { return $this->belongsTo(User::class, 'created_by'); }
    public function penugasanReviewer() { return $this->hasMany(PenugasanReviewer::class); }
    public function statusLog()         { return $this->hasMany(KegiatanStatusLog::class)->orderByDesc('created_at'); }
    public function berkas()            { return $this->hasMany(KegiatanBerkas::class)->orderByDesc('uploaded_at'); }
    public function luaran()            { return $this->hasMany(KegiatanLuaran::class); }

    public function anggota()
    {
        return $this->belongsToMany(Dosen::class, 'kegiatan_anggota', 'kegiatan_id', 'dosen_id')
                    ->withPivot('peran')->withTimestamps();
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'TERDAFTAR'     => 'Terdaftar',
            'BERJALAN'      => 'Berjalan',
            'LAPORAN_MASUK' => 'Laporan Masuk',
            'DINILAI'       => 'Dinilai',
            'SELESAI'       => 'Selesai',
            default         => $this->status,
        };
    }

    public function statusChip(): string
    {
        return match($this->status) {
            'TERDAFTAR'     => 'chip-terdaftar',
            'BERJALAN'      => 'chip-berjalan',
            'LAPORAN_MASUK' => 'chip-laporan',
            'DINILAI'       => 'chip-dinilai',
            'SELESAI'       => 'chip-selesai',
            default         => '',
        };
    }

    public function nextStatuses(): array
    {
        return match($this->status) {
            'TERDAFTAR'     => ['BERJALAN'],
            'BERJALAN'      => ['LAPORAN_MASUK'],
            'LAPORAN_MASUK' => ['DINILAI', 'BERJALAN'],
            'DINILAI'       => ['SELESAI'],
            default         => [],
        };
    }

    public function hitungSkorFinal(): void
    {
        $penilaianFinal = $this->penugasanReviewer()
            ->whereHas('penilaian', fn($q) => $q->where('status', 'FINAL'))
            ->with('penilaian')
            ->get()
            ->pluck('penilaian')
            ->filter();

        if ($penilaianFinal->isEmpty()) return;

        $skor = $penilaianFinal->avg('skor_akhir');

        $rekCount = $penilaianFinal->countBy('rekomendasi');
        $rekFinal = $rekCount->sortDesc()->keys()->first();

        $this->update(['skor_final' => round($skor, 2), 'rekomendasi_final' => $rekFinal]);
    }
}

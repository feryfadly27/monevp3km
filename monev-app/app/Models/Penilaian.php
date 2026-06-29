<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table    = 'penilaian';
    protected $fillable = ['penugasan_id', 'skor_akhir', 'rekomendasi', 'catatan', 'status', 'dinilai_at'];

    protected $casts = ['dinilai_at' => 'datetime', 'skor_akhir' => 'float'];

    public function penugasan() { return $this->belongsTo(PenugasanReviewer::class, 'penugasan_id'); }
    public function detail()    { return $this->hasMany(PenilaianDetail::class, 'penilaian_id'); }
}

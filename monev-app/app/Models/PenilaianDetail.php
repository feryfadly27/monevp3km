<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianDetail extends Model
{
    protected $table    = 'penilaian_detail';
    protected $fillable = ['penilaian_id', 'kriteria_id', 'skor', 'catatan'];

    protected $casts = ['skor' => 'float'];

    public function penilaian() { return $this->belongsTo(Penilaian::class); }
    public function kriteria()  { return $this->belongsTo(KriteriaPenilaian::class, 'kriteria_id'); }
}

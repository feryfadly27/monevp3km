<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaPenilaian extends Model
{
    protected $table = 'kriteria_penilaian';
    protected $fillable = ['scope', 'kategori_id', 'skema_id', 'nama', 'bobot', 'skor_min', 'skor_max', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean', 'bobot' => 'float'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class, 'skema_id');
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanLuaran extends Model
{
    protected $table    = 'kegiatan_luaran';
    protected $fillable = ['kegiatan_id', 'skema_luaran_id', 'jenis', 'judul_luaran', 'url_bukti', 'status_capaian', 'keterangan'];

    public function kegiatan()    { return $this->belongsTo(Kegiatan::class); }
    public function skemaLuaran() { return $this->belongsTo(SkemaLuaran::class, 'skema_luaran_id'); }
}

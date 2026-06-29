<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanBerkas extends Model
{
    protected $table    = 'kegiatan_berkas';
    protected $fillable = ['kegiatan_id', 'jenis', 'nama_file', 'path', 'ukuran_byte', 'uploaded_by', 'uploaded_at'];

    protected $casts = ['uploaded_at' => 'datetime'];

    public function kegiatan()    { return $this->belongsTo(Kegiatan::class); }
    public function uploadedBy()  { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function ukuranLabel(): string
    {
        $b = $this->ukuran_byte;
        if ($b >= 1048576) return number_format($b / 1048576, 1) . ' MB';
        if ($b >= 1024)    return number_format($b / 1024, 1) . ' KB';
        return $b . ' B';
    }
}

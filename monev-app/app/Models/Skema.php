<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skema extends Model
{
    protected $table = 'skema';
    protected $fillable = ['kategori_id', 'kode', 'nama', 'dana_maksimal', 'durasi_bulan', 'deskripsi', 'aktif'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function luaran()
    {
        return $this->hasMany(SkemaLuaran::class);
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class);
    }
}

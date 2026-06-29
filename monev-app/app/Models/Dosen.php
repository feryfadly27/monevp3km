<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $fillable = ['user_id', 'prodi_id', 'nidn', 'nama', 'email', 'no_hp'];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'ketua_dosen_id');
    }

    public function kegiatanAnggota()
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_anggota', 'dosen_id', 'kegiatan_id')
                    ->withPivot('peran')->withTimestamps();
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $table = 'prodi';
    protected $fillable = ['fakultas_id', 'nama'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'prodi_id');
    }
}

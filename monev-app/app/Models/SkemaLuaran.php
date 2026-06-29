<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkemaLuaran extends Model
{
    protected $table    = 'skema_luaran';
    protected $fillable = ['skema_id', 'jenis', 'deskripsi', 'wajib', 'jumlah_minimal'];

    protected $casts = ['wajib' => 'boolean'];

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }
}

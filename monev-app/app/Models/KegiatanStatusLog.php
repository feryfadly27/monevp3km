<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanStatusLog extends Model
{
    protected $table    = 'kegiatan_status_log';
    public    $timestamps = false;
    protected $fillable = ['kegiatan_id', 'status_lama', 'status_baru', 'oleh_user_id', 'catatan', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function kegiatan() { return $this->belongsTo(Kegiatan::class); }
    public function oleh()     { return $this->belongsTo(User::class, 'oleh_user_id'); }
}

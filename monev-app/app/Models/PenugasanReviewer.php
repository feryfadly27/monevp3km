<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenugasanReviewer extends Model
{
    protected $table = 'penugasan_reviewer';
    protected $fillable = ['kegiatan_id', 'reviewer_user_id', 'assigned_by', 'status'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class, 'penugasan_id');
    }
}

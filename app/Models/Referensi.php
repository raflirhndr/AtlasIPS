<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referensi extends Model
{
    protected $fillable = ['materi_id', 'judul', 'source_type', 'url', 'file_pdf', 'deskripsi'];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}

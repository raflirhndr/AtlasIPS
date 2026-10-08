<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Materi extends Model { protected $fillable=['bab_id','judul','isi','urutan']; public function bab(){return $this->belongsTo(Bab::class);} public function videos(){return $this->hasMany(Video::class);} public function referensi(){return $this->hasMany(Referensi::class);} }

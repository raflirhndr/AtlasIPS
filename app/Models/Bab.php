<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Bab extends Model { protected $fillable=['judul','deskripsi','urutan']; public function materi(){return $this->hasMany(Materi::class)->orderBy('urutan');} }

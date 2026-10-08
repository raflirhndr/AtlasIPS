<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::create('referensis',function(Blueprint $t){$t->id();$t->foreignId('materi_id')->constrained('materis')->cascadeOnDelete();$t->string('judul');$t->text('url');$t->text('deskripsi')->nullable();$t->timestamps();});}public function down():void{Schema::dropIfExists('referensis');}};

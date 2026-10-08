<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::create('materis',function(Blueprint $t){$t->id();$t->foreignId('bab_id')->constrained('babs')->cascadeOnDelete();$t->string('judul');$t->longText('isi');$t->unsignedInteger('urutan')->default(1);$t->timestamps();});}public function down():void{Schema::dropIfExists('materis');}};

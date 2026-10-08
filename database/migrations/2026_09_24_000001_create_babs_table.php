<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::create('babs',function(Blueprint $t){$t->id();$t->string('judul');$t->text('deskripsi')->nullable();$t->unsignedInteger('urutan')->default(1);$t->timestamps();});}public function down():void{Schema::dropIfExists('babs');}};

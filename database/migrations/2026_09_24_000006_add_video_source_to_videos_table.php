<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('source_type', 20)->default('upload')->after('judul');
            $table->text('video_url')->nullable()->after('file_video');
            $table->string('file_video')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'video_url']);
            $table->string('file_video')->nullable(false)->change();
        });
    }
};

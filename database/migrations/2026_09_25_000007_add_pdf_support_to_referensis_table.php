<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referensis', function (Blueprint $table) {
            $table->string('source_type')->default('link')->after('judul');
            $table->string('file_pdf')->nullable()->after('url');
        });
    }

    public function down(): void
    {
        Schema::table('referensis', function (Blueprint $table) {
            $table->dropColumn(['source_type', 'file_pdf']);
        });
    }
};

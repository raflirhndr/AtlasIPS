<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('babs')) {
            DB::table('babs')
                ->orderBy('urutan')
                ->orderBy('id')
                ->get()
                ->each(function ($bab, $index) {
                    DB::table('babs')->where('id', $bab->id)->update([
                        'urutan' => $index + 1,
                    ]);
                });
        }

        if (Schema::hasTable('materis')) {
            DB::table('babs')->orderBy('id')->pluck('id')->each(function ($babId) {
                DB::table('materis')
                    ->where('bab_id', $babId)
                    ->orderBy('urutan')
                    ->orderBy('id')
                    ->get()
                    ->each(function ($materi, $index) {
                        DB::table('materis')->where('id', $materi->id)->update([
                            'urutan' => $index + 1,
                        ]);
                    });
            });
        }
    }

    public function down(): void
    {
        // The migration normalizes derived ordering values; there is no safe
        // inverse because the previous gaps/order values are not recoverable.
    }
};

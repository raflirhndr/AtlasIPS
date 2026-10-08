<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('videos:organize', function () {
    $videos = \App\Models\Video::with('materi')->get();
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $moved = 0;
    $missing = 0;

    foreach ($videos as $video) {
        if (!$video->file_video || !$video->materi) {
            $missing++;
            continue;
        }

        $directory = "videos/bab-{$video->materi->bab_id}/materi-{$video->materi_id}";
        $filename = basename($video->file_video);
        $target = $directory.'/'.$filename;

        if ($video->file_video === $target) {
            continue;
        }

        if (!$disk->exists($video->file_video)) {
            $missing++;
            continue;
        }

        if ($disk->exists($target)) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $filename = $base.'-'.uniqid().($extension ? '.'.$extension : '');
            $target = $directory.'/'.$filename;
        }

        $disk->move($video->file_video, $target);
        $video->update(['file_video' => $target]);
        $moved++;
    }

    $this->info("Selesai. {$moved} video dipindahkan.");
    if ($missing > 0) {
        $this->warn("{$missing} video dilewati karena file/materi tidak ditemukan.");
    }
})->purpose('Merapikan video lama ke folder berdasarkan Bab dan Materi');

<?php

namespace Tests\Feature;

use App\Models\Bab;
use App\Models\Materi;
use App\Models\Referensi;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_materi_removes_uploaded_video_and_pdf(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-materi@example.test',
            'password' => 'TestPassword123!',
            'role' => 'admin',
        ]);

        $bab = Bab::create(['judul' => 'Bab Uji', 'deskripsi' => null, 'urutan' => 1]);
        $materi = Materi::create([
            'bab_id' => $bab->id,
            'judul' => 'Materi Uji',
            'isi' => 'Isi uji',
            'urutan' => 1,
        ]);

        Storage::disk('public')->put("videos/bab-{$bab->id}/materi-{$materi->id}/video.mp4", 'video');
        Storage::disk('public')->put('references/reference.pdf', 'pdf');

        $videoPath = "videos/bab-{$bab->id}/materi-{$materi->id}/video.mp4";

        Video::create([
            'materi_id' => $materi->id,
            'judul' => 'Video Uji',
            'source_type' => 'upload',
            'file_video' => $videoPath,
            'video_url' => null,
            'deskripsi' => null,
        ]);

        Referensi::create([
            'materi_id' => $materi->id,
            'judul' => 'PDF Uji',
            'source_type' => 'pdf',
            'url' => null,
            'file_pdf' => 'references/reference.pdf',
            'deskripsi' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.materi.destroy', $materi))
            ->assertRedirect();

        Storage::disk('public')->assertMissing($videoPath);
        Storage::disk('public')->assertMissing('references/reference.pdf');
    }

    public function test_deleting_bab_removes_uploaded_video_and_pdf(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin-bab@example.test',
            'password' => 'TestPassword123!',
            'role' => 'admin',
        ]);

        $bab = Bab::create(['judul' => 'Bab Uji', 'deskripsi' => null, 'urutan' => 1]);
        $materi = Materi::create([
            'bab_id' => $bab->id,
            'judul' => 'Materi Uji',
            'isi' => 'Isi uji',
            'urutan' => 1,
        ]);

        Storage::disk('public')->put("videos/bab-{$bab->id}/materi-{$materi->id}/video.mp4", 'video');
        Storage::disk('public')->put('references/reference.pdf', 'pdf');

        $videoPath = "videos/bab-{$bab->id}/materi-{$materi->id}/video.mp4";

        Video::create([
            'materi_id' => $materi->id,
            'judul' => 'Video Uji',
            'source_type' => 'upload',
            'file_video' => $videoPath,
            'video_url' => null,
            'deskripsi' => null,
        ]);

        Referensi::create([
            'materi_id' => $materi->id,
            'judul' => 'PDF Uji',
            'source_type' => 'pdf',
            'url' => null,
            'file_pdf' => 'references/reference.pdf',
            'deskripsi' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.bab.destroy', $bab))
            ->assertRedirect();

        Storage::disk('public')->assertMissing($videoPath);
        Storage::disk('public')->assertMissing('references/reference.pdf');
    }
}

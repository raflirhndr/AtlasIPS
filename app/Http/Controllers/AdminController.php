<?php
namespace App\Http\Controllers;

use App\Models\Bab;
use App\Models\Materi;
use App\Models\Referensi;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private function ensureAdmin()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);
    }

    public function index()
    {
        $this->ensureAdmin();
        return view('admin.index', [
            'babs' => Bab::withCount('materi')->orderBy('urutan')->get(),
            'materis' => Materi::with('bab')->orderBy('bab_id')->orderBy('urutan')->get(),
            'videos' => Video::with('materi.bab')->latest()->get(),
            'referensis' => Referensi::with('materi.bab')->latest()->get(),
        ]);
    }

    public function storeBab(Request $r)
    {
        $this->ensureAdmin();

        $data = $r->validate([
            'judul' => 'required|max:255',
            'urutan' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Bab::create($data);

        return back()->with('ok','Bab berhasil ditambahkan.');
    }

    public function updateBab(Request $r, Bab $bab)
    {
        $this->ensureAdmin();

        $bab->update($r->validate([
            'judul' => 'required|max:255',
            'urutan' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]));

        return back()->with('ok','Bab berhasil diperbarui.');
    }

    public function destroyBab(Bab $bab)
    {
        $this->ensureAdmin();
        foreach ($bab->materi as $materi) {
            foreach ($materi->videos as $video) {
                if ($video->file_video) {
                    Storage::disk('public')->delete($video->file_video);
                }
            }

            foreach ($materi->referensi as $referensi) {
                if ($referensi->file_pdf) {
                    Storage::disk('public')->delete($referensi->file_pdf);
                }
            }
        }
        $bab->delete();
        $this->normalizeBabOrder();

        return back()->with('ok','Bab berhasil dihapus.');
    }

    public function storeMateri(Request $r)
    {
        $this->ensureAdmin();

        $data = $r->validate([
            'bab_id' => 'required|exists:babs,id',
            'judul' => 'required|max:255',
            'urutan' => 'required|integer|min:1',
            'isi' => 'required|string',
        ]);

        Materi::create($data);

        return back()->with('ok','Materi berhasil ditambahkan.');
    }

    public function updateMateri(Request $r, Materi $materi)
    {
        $this->ensureAdmin();

        $oldBabId = $materi->bab_id;
        $data = $r->validate([
            'bab_id' => 'required|exists:babs,id',
            'judul' => 'required|max:255',
            'urutan' => 'required|integer|min:1',
            'isi' => 'required|string',
        ]);

        $materi->update($data);

        return back()->with('ok','Materi berhasil diperbarui.');
    }

    public function destroyMateri(Materi $materi)
    {
        $this->ensureAdmin();
        foreach ($materi->videos as $video) {
            if ($video->file_video) {
                Storage::disk('public')->delete($video->file_video);
            }
        }

        foreach ($materi->referensi as $referensi) {
            if ($referensi->file_pdf) {
                Storage::disk('public')->delete($referensi->file_pdf);
            }
        }

        $babId = $materi->bab_id;
        $materi->delete();
        $this->normalizeMateriOrder($babId);

        return back()->with('ok','Materi berhasil dihapus.');
    }

    /**
     * Menjaga nomor Bab tetap berurutan 1..N.
     */
    private function normalizeBabOrder(): void
    {
        Bab::orderBy('urutan')->orderBy('id')->get()->each(function (Bab $bab, int $index) {
            $newOrder = $index + 1;
            if ((int) $bab->urutan !== $newOrder) {
                $bab->updateQuietly(['urutan' => $newOrder]);
            }
        });
    }

    /**
     * Menjaga nomor Materi tetap berurutan 1..N di dalam setiap Bab.
     */
    private function normalizeMateriOrder(int $babId): void
    {
        Materi::where('bab_id', $babId)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->each(function (Materi $materi, int $index) {
                $newOrder = $index + 1;
                if ((int) $materi->urutan !== $newOrder) {
                    $materi->updateQuietly(['urutan' => $newOrder]);
                }
            });
    }

    /**
     * Menentukan folder video berdasarkan Bab dan Materi.
     * File upload tetap berada di storage Laravel, sedangkan MySQL hanya
     * menyimpan path file. Video berbasis link tidak memakai storage.
     */
    private function videoDirectory(Materi $materi): string
    {
        return "videos/bab-{$materi->bab_id}/materi-{$materi->id}";
    }

    private function moveVideoToDirectory(?string $path, Materi $materi): ?string
    {
        if (!$path) return $path;
        $disk = Storage::disk('public');
        $directory = $this->videoDirectory($materi);
        $filename = basename($path);
        $newPath = $directory.'/'.$filename;
        if ($path === $newPath) return $path;
        if (!$disk->exists($path)) return $path;
        if ($disk->exists($newPath)) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $filename = $base.'-'.uniqid().($extension ? '.'.$extension : '');
            $newPath = $directory.'/'.$filename;
        }
        $disk->move($path, $newPath);
        return $newPath;
    }

    /**
     * Mengembalikan true jika URL benar-benar menunjuk ke video YouTube yang valid.
     * Domain lain tidak diterima untuk sumber video YouTube.
     */
    private function youtubeVideoId(string $url): ?string
    {
        $parts = parse_url(trim($url));
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        $host = preg_replace('/^www\./', '', $host);
        $path = trim((string) ($parts['path'] ?? ''), '/');

        if (!in_array($scheme, ['http', 'https'], true)) return null;

        if ($host === 'youtu.be') {
            $id = explode('/', $path)[0] ?? '';
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'youtube-nocookie.com'], true)) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            $id = (string) ($query['v'] ?? '');

            if ($id === '' && preg_match('#^(?:embed|shorts)/([^/]+)$#', $path, $matches)) {
                $id = $matches[1];
            }
        } else {
            return null;
        }

        return preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? $id : null;
    }

    private function videoUrlType(string $url): string
    {
        return $this->youtubeVideoId($url) !== null ? 'youtube' : 'unknown';
    }

    private function validateVideoSource(Request $r): array
    {
        $sourceType = $r->input('source_type', 'upload');
        abort_unless(in_array($sourceType, ['upload', 'youtube'], true), 422);

        $rules = [
            'materi_id' => 'required|exists:materis,id',
            'judul' => 'required|max:255',
            'deskripsi' => 'nullable|string',
            'source_type' => 'required|in:upload,youtube',
        ];

        if ($sourceType === 'upload') {
            $rules['file_video'] = 'required|file|mimes:mp4,webm,ogg|max:204800';
        } else {
            $rules['video_url'] = 'required|url|max:2048';
        }

        $data = $r->validate($rules);

        if ($sourceType === 'youtube') {
            if ($this->videoUrlType($data['video_url']) !== 'youtube') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'video_url' => 'Gunakan URL video YouTube yang valid, misalnya https://www.youtube.com/watch?v=xxxxxxxxxxx.'
                ]);
            }
        }

        return $data;
    }

    public function storeVideo(Request $r)
    {
        $this->ensureAdmin();
        $data = $this->validateVideoSource($r);
        $materi = Materi::with('bab')->findOrFail($data['materi_id']);

        if ($data['source_type'] === 'upload') {
            $data['file_video'] = $r->file('file_video')->store($this->videoDirectory($materi), 'public');
            $data['video_url'] = null;
        } else {
            $data['file_video'] = null;
        }

        Video::create($data);
        return back()->with('ok', 'Video berhasil ditambahkan.');
    }

    public function updateVideo(Request $r, Video $video)
    {
        $this->ensureAdmin();
        $data = $this->validateVideoSourceForUpdate($r, $video);
        $materi = Materi::with('bab')->findOrFail($data['materi_id']);
        $oldPath = $video->file_video;
        $newType = $data['source_type'];

        if ($newType === 'upload') {
            if ($r->hasFile('file_video')) {
                $data['file_video'] = $r->file('file_video')->store($this->videoDirectory($materi), 'public');
                if ($oldPath) Storage::disk('public')->delete($oldPath);
            } else {
                $data['file_video'] = $this->moveVideoToDirectory($oldPath, $materi);
            }
            $data['video_url'] = null;
        } else {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            $data['file_video'] = null;
        }

        $video->update($data);
        return back()->with('ok', 'Video berhasil diperbarui.');
    }

    private function validateVideoSourceForUpdate(Request $r, Video $video): array
    {
        $sourceType = $r->input('source_type', $video->source_type ?: ($video->file_video ? 'upload' : 'direct'));
        abort_unless(in_array($sourceType, ['upload', 'youtube'], true), 422);

        $rules = [
            'materi_id' => 'required|exists:materis,id',
            'judul' => 'required|max:255',
            'deskripsi' => 'nullable|string',
            'source_type' => 'required|in:upload,youtube',
        ];

        if ($sourceType === 'upload') {
            $required = $video->file_video ? 'nullable' : 'required';
            $rules['file_video'] = $required.'|file|mimes:mp4,webm,ogg|max:204800';
        } else {
            $rules['video_url'] = 'required|url|max:2048';
        }

        $data = $r->validate($rules);

        if ($sourceType === 'youtube') {
            if ($this->videoUrlType($data['video_url']) !== 'youtube') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'video_url' => 'Gunakan URL video YouTube yang valid, misalnya https://www.youtube.com/watch?v=xxxxxxxxxxx.'
                ]);
            }
        }
        return $data;
    }

    public function destroyVideo(Video $video)
    {
        $this->ensureAdmin();
        if ($video->file_video) Storage::disk('public')->delete($video->file_video);
        $video->delete();
        return back()->with('ok', 'Video berhasil dihapus.');
    }

    /**
     * Referensi mendukung dua sumber:
     * - link website: menyimpan URL
     * - PDF: file disimpan di storage Laravel, database hanya menyimpan path
     */
    private function validateReferensi(Request $r, ?Referensi $referensi = null): array
    {
        $sourceType = $r->input('source_type', $referensi?->source_type ?: 'link');
        abort_unless(in_array($sourceType, ['link', 'pdf'], true), 422);

        $rules = [
            'materi_id' => 'required|exists:materis,id',
            'judul' => 'required|max:255',
            'source_type' => 'required|in:link,pdf',
            'deskripsi' => 'nullable|string',
        ];

        if ($sourceType === 'link') {
            $rules['url'] = 'required|url|max:2048';
            $rules['file_pdf'] = 'nullable|file|mimes:pdf|max:20480';
        } else {
            $hasExistingPdf = $referensi?->file_pdf;
            $rules['file_pdf'] = ($hasExistingPdf ? 'nullable' : 'required') . '|file|mimes:pdf|max:20480';
            $rules['url'] = 'nullable|url|max:2048';
        }

        return $r->validate($rules);
    }

    public function storeReferensi(Request $r)
    {
        $this->ensureAdmin();
        $data = $this->validateReferensi($r);

        if ($data['source_type'] === 'pdf') {
            $data['file_pdf'] = $r->file('file_pdf')->store('references', 'public');
            $data['url'] = null;
        } else {
            $data['file_pdf'] = null;
        }

        Referensi::create($data);
        return back()->with('ok','Referensi berhasil ditambahkan.');
    }

    public function updateReferensi(Request $r, Referensi $referensi)
    {
        $this->ensureAdmin();
        $data = $this->validateReferensi($r, $referensi);
        $oldFile = $referensi->file_pdf;

        if ($data['source_type'] === 'pdf') {
            if ($r->hasFile('file_pdf')) {
                $data['file_pdf'] = $r->file('file_pdf')->store('references', 'public');
                if ($oldFile) Storage::disk('public')->delete($oldFile);
            } else {
                $data['file_pdf'] = $oldFile;
            }
            $data['url'] = null;
        } else {
            if ($oldFile) Storage::disk('public')->delete($oldFile);
            $data['file_pdf'] = null;
        }

        $referensi->update($data);
        return back()->with('ok','Referensi berhasil diperbarui.');
    }

    public function destroyReferensi(Referensi $referensi)
    {
        $this->ensureAdmin();
        if ($referensi->file_pdf) {
            Storage::disk('public')->delete($referensi->file_pdf);
        }
        $referensi->delete();
        return back()->with('ok','Referensi berhasil dihapus.');
    }
}

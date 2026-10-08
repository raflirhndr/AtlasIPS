<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'materi_id',
        'judul',
        'source_type',
        'file_video',
        'video_url',
        'deskripsi',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function embedUrl(): ?string
    {
        if (!$this->video_url) return null;
        $url = $this->video_url;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host);

        if (in_array($host, ['youtube.com', 'youtube-nocookie.com', 'youtu.be'], true)) {
            $id = null;
            if ($host === 'youtu.be') {
                $id = trim((string) parse_url($url, PHP_URL_PATH), '/');
            } else {
                parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
                $id = $query['v'] ?? null;
                if (!$id && preg_match('#/(?:embed|shorts)/([^/?]+)#', (string) parse_url($url, PHP_URL_PATH), $m)) $id = $m[1];
            }
            return $id ? 'https://www.youtube-nocookie.com/embed/'.rawurlencode($id) : null;
        }

        if ($host === 'vimeo.com') {
            $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
            if (preg_match('/(\d+)/', $path, $m)) return 'https://player.vimeo.com/video/'.$m[1];
        }

        return null;
    }
}

@extends('layouts.app')
@section('title','Dashboard Admin · ATLAS IPS')
@section('content')
<div class="admin-head">
    <div>
        <span class="eyebrow">PENGELOLAAN WEBSITE</span>
        <h1>Dashboard Admin</h1>
        <p>Kelola Bab, materi, video, dan referensi dari satu tempat.</p>
    </div>
    <div class="admin-badge">👋 {{ auth()->user()->name }}</div>
</div>

<div class="stats">
    <div><strong>{{ $babs->count() }}</strong><span>Bab</span></div>
    <div><strong>{{ $materis->count() }}</strong><span>Materi</span></div>
    <div><strong>{{ $videos->count() }}</strong><span>Video</span></div>
    <div><strong>{{ $referensis->count() }}</strong><span>Referensi</span></div>
</div>

<div class="admin-toolbar">
    <div>
        <strong>Menu Pengelolaan</strong>
        <small>Pilih bagian yang ingin dikelola.</small>
    </div>
    <div class="admin-tabs" role="tablist">
        <button type="button" class="admin-tab active" data-admin-tab="bab">📚 Bab</button>
        <button type="button" class="admin-tab" data-admin-tab="materi">📖 Materi</button>
        <button type="button" class="admin-tab" data-admin-tab="video">🎬 Video</button>
        <button type="button" class="admin-tab" data-admin-tab="referensi">🔗 Referensi</button>
    </div>
</div>

<section class="admin-section admin-panel active" data-admin-panel="bab">
    <div class="admin-section-head"><div><span class="eyebrow">1 · STRUKTUR PEMBELAJARAN</span><h2>Bab</h2><p>Setiap Bab menjadi kelompok utama pembelajaran siswa.</p></div></div>
    <form class="admin-form" method="post" action="{{ route('admin.bab.store') }}">
        @csrf
        <div><label>Judul Bab</label><input name="judul" value="{{ old('judul') }}" placeholder="Contoh: Pengenalan Algoritma" required></div>
        <div><label>Nomor Bab</label><input type="number" name="urutan" min="1" value="{{ old('urutan', ((int) $babs->max('urutan')) + 1) }}" required><small class="field-help"></small></div>
        <div class="wide"><label>Deskripsi</label><input name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Ringkasan singkat Bab"></div>
        <button class="primary">+ Tambah Bab</button>
    </form>
    <div class="admin-list-tools"><input class="admin-filter" data-filter-target="bab-list" type="search" placeholder="Cari Bab..."></div>
    <div class="admin-table" id="bab-list">
        @forelse($babs as $b)
        <div class="row-card filter-item" data-filter-text="{{ strtolower($b->judul.' '.$b->deskripsi) }}">
            <div class="row-main"><span class="number">{{ $b->urutan }}</span><div><strong>{{ $b->judul }}</strong><small>{{ $b->materi_count }} materi @if($b->deskripsi) · {{ $b->deskripsi }} @endif</small></div></div>
            <details><summary>Edit</summary><form method="post" action="{{ route('admin.bab.update',$b) }}" class="mini-form vertical">@csrf @method('PUT')<input name="judul" value="{{ $b->judul }}" placeholder="Judul Bab" required><input type="number" name="urutan" min="1" value="{{ $b->urutan }}" required><input name="deskripsi" value="{{ $b->deskripsi }}" placeholder="Deskripsi"><button>Simpan</button></form></details>
            <form method="post" action="{{ route('admin.bab.destroy',$b) }}" onsubmit="return confirmDelete('Bab ini dan seluruh materi di dalamnya akan dihapus.')">@csrf @method('DELETE')<button class="danger">Hapus</button></form>
        </div>
        @empty<div class="empty">Belum ada Bab.</div>@endforelse
    </div>
</section>

<section class="admin-section admin-panel" data-admin-panel="materi">
    <div class="admin-section-head"><div><span class="eyebrow">2 · ISI PEMBELAJARAN</span><h2>Materi</h2><p>Tulis isi materi yang akan dibaca siswa.</p></div></div>
    @if($babs->isEmpty())
        <div class="empty">Buat Bab terlebih dahulu sebelum menambahkan materi.</div>
    @else
    <form class="admin-form stacked" method="post" action="{{ route('admin.materi.store') }}">
        @csrf
        <div><label>Bab</label><select name="bab_id" required>@foreach($babs as $b)<option value="{{ $b->id }}">Bab {{ $b->urutan }} · {{ $b->judul }}</option>@endforeach</select></div>
        <div><label>Judul Materi</label><input name="judul" placeholder="Contoh: Pengertian Algoritma" required></div>
        <div><label>Nomor Materi</label><input id="materi-order-input" type="number" name="urutan" min="1" value="{{ old('urutan', $babs->isNotEmpty() ? ((int) $babs->first()->materi()->max('urutan')) + 1 : 1) }}" required><small class="field-help"></small></div>
        <div class="wide"><label>Isi Materi</label><textarea name="isi" rows="8" placeholder="Tulis materi pembelajaran di sini..." required></textarea><small class="field-help">Gunakan paragraf yang jelas dan mudah dibaca siswa SMP.</small></div>
        <button class="primary">+ Tambah Materi</button>
    </form>
    <div class="admin-list-tools"><input class="admin-filter" data-filter-target="materi-list" type="search" placeholder="Cari judul materi atau Bab..."></div>
    <div class="admin-table" id="materi-list">
        @forelse($materis as $m)
        <div class="row-card column-mobile filter-item" data-filter-text="{{ strtolower($m->judul.' '.$m->bab->judul) }}">
            <div class="row-main"><span class="material-icon">📖</span><div><strong>{{ $m->judul }}</strong><small>Bab {{ $m->bab->urutan }} · {{ $m->bab->judul }}</small></div></div>
            <details><summary>Edit</summary><form method="post" action="{{ route('admin.materi.update',$m) }}" class="mini-form vertical">@csrf @method('PUT')<select name="bab_id">@foreach($babs as $b)<option value="{{ $b->id }}" @selected($m->bab_id==$b->id)>Bab {{ $b->urutan }} · {{ $b->judul }}</option>@endforeach</select><input name="judul" value="{{ $m->judul }}" required><input type="number" name="urutan" min="1" value="{{ $m->urutan }}" required><textarea name="isi" rows="7" required>{{ $m->isi }}</textarea><button>Simpan</button></form></details>
            <form method="post" action="{{ route('admin.materi.destroy',$m) }}" onsubmit="return confirmDelete('Materi ini beserta video di dalamnya akan dihapus.')">@csrf @method('DELETE')<button class="danger">Hapus</button></form>
        </div>
        @empty<div class="empty">Belum ada materi.</div>@endforelse
    </div>
    @endif
</section>

<section class="admin-section admin-panel" data-admin-panel="video">
    <div class="admin-section-head"><div><span class="eyebrow">3 · MEDIA PEMBELAJARAN</span><h2>Video Pembelajaran</h2><p>Pilih video sendiri atau gunakan link YouTube. Video upload memakai storage aplikasi; video YouTube hanya menyimpan URL.</p></div></div>
    @if($materis->isEmpty())
        <div class="empty">Buat materi terlebih dahulu sebelum menambahkan video.</div>
    @else
    <form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.video.store') }}" id="video-upload-form">
        @csrf
        <div class="wide"><label>Materi</label><select name="materi_id" required>@foreach($materis as $m)<option value="{{ $m->id }}">Bab {{ $m->bab->urutan }} · {{ $m->judul }}</option>@endforeach</select></div>
        <div><label>Judul Video</label><input name="judul" placeholder="Judul video" required></div>
        <div><label>Sumber Video</label><select name="source_type" id="video-source-type" required><option value="upload">📁 Video Sendiri</option><option value="youtube">▶ YouTube</option></select></div>
        <div id="video-upload-field"><label>File Video</label><input id="video-file-input" type="file" name="file_video" accept="video/mp4,video/webm,video/ogg"><small class="field-help">Format: MP4, WebM, OGG · Maks. 200 MB.</small></div>
        <div class="wide" id="video-url-field" hidden><label>URL Video</label><input id="video-url-input" type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." disabled><small class="field-help">Masukkan URL video YouTube, misalnya https://www.youtube.com/watch?v=...</small></div>
        <div class="wide"><label>Deskripsi</label><input name="deskripsi" placeholder="Keterangan video (opsional)"></div>
        <div class="wide video-preview-box" id="video-preview-box" hidden>
            <div class="preview-label">Preview sebelum upload</div>
            <video id="video-preview" controls preload="metadata"></video>
            <div id="video-file-info" class="field-help"></div>
        </div>
        <button class="primary">＋ Simpan Video</button>
    </form>
    <div class="admin-list-tools"><input class="admin-filter" data-filter-target="video-list" type="search" placeholder="Cari video atau materi..."></div>
    <div class="admin-table" id="video-list">
        @forelse($videos as $v)
        <div class="row-card filter-item" data-filter-text="{{ strtolower($v->judul.' '.$v->materi->judul.' '.$v->materi->bab->judul) }}">
            <div class="row-main"><span class="material-icon">🎬</span><div><strong>{{ $v->judul }}</strong><small>Bab {{ $v->materi->urutan }} · {{ $v->materi->judul }}</small></div></div>
            <details><summary>Edit</summary><form method="post" enctype="multipart/form-data" action="{{ route('admin.video.update',$v) }}" class="mini-form vertical admin-video-edit-form" data-existing-source="{{ $v->source_type }}" data-has-file="{{ $v->file_video ? '1' : '0' }}">
                @csrf @method('PUT')
                <select name="materi_id">@foreach($materis as $m)<option value="{{ $m->id }}" @selected($v->materi_id==$m->id)>Bab {{ $m->bab->urutan }} · {{ $m->judul }}</option>@endforeach</select>
                <input name="judul" value="{{ $v->judul }}" required>
                <select name="source_type" class="edit-video-source" required><option value="upload" @selected($v->source_type==='upload')>📁 Video Sendiri</option><option value="youtube" @selected($v->source_type==='youtube')>▶ YouTube</option></select>
                <div class="edit-video-file"><input type="file" name="file_video" accept="video/mp4,video/webm,video/ogg"><small>Upload file baru hanya jika ingin mengganti video.</small></div>
                <div class="edit-video-url" @if($v->source_type==='upload') hidden @endif><input type="url" name="video_url" value="{{ $v->video_url }}" placeholder="https://..." @disabled($v->source_type==='upload')></div>
                <input name="deskripsi" value="{{ $v->deskripsi }}" placeholder="Deskripsi">
                <button>Simpan</button>
            </form></details>
            <form method="post" action="{{ route('admin.video.destroy',$v) }}" onsubmit="return confirmDelete('Video ini akan dihapus dari database dan storage jika berupa upload.')">@csrf @method('DELETE')<button class="danger">Hapus</button></form>
        </div>
        @empty<div class="empty">Belum ada video.</div>@endforelse
    </div>
    @endif
</section>

<section class="admin-section admin-panel" data-admin-panel="referensi">
    <div class="admin-section-head"><div><span class="eyebrow">4 · REFERENSI</span><h2>Referensi Tambahan</h2><p>Tambahkan sumber lain yang masih berkaitan dengan materi.</p></div></div>
    @if($materis->isEmpty())
        <div class="empty">Buat materi terlebih dahulu sebelum menambahkan referensi.</div>
    @else
    <form class="admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.referensi.store') }}">
        @csrf
        <div class="wide"><label>Materi</label><select name="materi_id" required>@foreach($materis as $m)<option value="{{ $m->id }}">Bab {{ $m->bab->urutan }} · {{ $m->judul }}</option>@endforeach</select></div>
        <div><label>Judul Referensi</label><input name="judul" placeholder="Contoh: Belajar Algoritma" required></div>
        <div>
            <label>Jenis Referensi</label>
            <select name="source_type" class="referensi-source-type" required>
                <option value="link">🔗 Link Website</option>
                <option value="pdf">📄 File PDF</option>
            </select>
        </div>
        <div class="referensi-url-field">
            <label>URL Website</label>
            <input type="url" name="url" placeholder="https://...">
        </div>
        <div class="referensi-pdf-field" hidden>
            <label>File PDF</label>
            <input type="file" name="file_pdf" accept="application/pdf,.pdf" disabled>
            <small>PDF · maksimal 20 MB.</small>
        </div>
        <div class="wide"><label>Deskripsi</label><input name="deskripsi" placeholder="Keterangan singkat (opsional)"></div>
        <button class="primary">+ Tambah Referensi</button>
    </form>
    <div class="admin-list-tools"><input class="admin-filter" data-filter-target="referensi-list" type="search" placeholder="Cari referensi atau materi..."></div>
    <div class="admin-table" id="referensi-list">
        @forelse($referensis as $r)
        <div class="row-card filter-item" data-filter-text="{{ strtolower($r->judul.' '.$r->materi->judul.' '.$r->materi->bab->judul) }}">
            <div class="row-main">
                <span class="material-icon">{{ $r->source_type === 'pdf' ? '📄' : '🔗' }}</span>
                <div>
                    <strong>{{ $r->judul }}</strong>
                    <small>{{ $r->materi->judul }} · {{ $r->source_type === 'pdf' ? 'File PDF' : $r->url }}</small>
                </div>
            </div>
            <details>
                <summary>Edit</summary>
                <form method="post" enctype="multipart/form-data" action="{{ route('admin.referensi.update',$r) }}" class="mini-form vertical admin-referensi-edit-form" data-has-file="{{ $r->file_pdf ? '1' : '0' }}">
                    @csrf @method('PUT')
                    <select name="materi_id">@foreach($materis as $m)<option value="{{ $m->id }}" @selected($r->materi_id==$m->id)>Bab {{ $m->bab->urutan }} · {{ $m->judul }}</option>@endforeach</select>
                    <input name="judul" value="{{ $r->judul }}" required>
                    <select name="source_type" class="edit-referensi-source" required>
                        <option value="link" @selected($r->source_type === 'link')>🔗 Link Website</option>
                        <option value="pdf" @selected($r->source_type === 'pdf')>📄 File PDF</option>
                    </select>
                    <div class="edit-referensi-url" @if($r->source_type === 'pdf') hidden @endif>
                        <input type="url" name="url" value="{{ $r->url }}" placeholder="https://..." @disabled($r->source_type === 'pdf')>
                    </div>
                    <div class="edit-referensi-pdf" @if($r->source_type !== 'pdf') hidden @endif>
                        <input type="file" name="file_pdf" accept="application/pdf,.pdf" @disabled($r->source_type !== 'pdf')>
                        @if($r->file_pdf)<small>PDF saat ini tersimpan. Pilih file baru hanya jika ingin menggantinya.</small>@endif
                    </div>
                    <input name="deskripsi" value="{{ $r->deskripsi }}">
                    <button>Simpan</button>
                </form>
            </details>
            <form method="post" action="{{ route('admin.referensi.destroy',$r) }}" onsubmit="return confirmDelete('Referensi ini akan dihapus.')">@csrf @method('DELETE')<button class="danger">Hapus</button></form>
        </div>
        @empty<div class="empty">Belum ada referensi.</div>@endforelse
    </div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const babSelect = document.querySelector('select[name="bab_id"]');
    const orderInput = document.getElementById('materi-order-input');
    if (!babSelect || !orderInput) return;

    const nextOrders = @json($babs->mapWithKeys(function ($b) {
        return [$b->id => ((int) $b->materi()->max('urutan')) + 1];
    }));

    babSelect.addEventListener('change', function () {
        orderInput.value = nextOrders[this.value] ?? 1;
    });
});
</script>

@endsection

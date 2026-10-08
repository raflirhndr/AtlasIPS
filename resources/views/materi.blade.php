@extends('layouts.app')
@section('title',$materi->judul.' · ATLAS IPS')
@section('content')
<div class="breadcrumb"><a href="{{ route('home') }}">Beranda</a> / <a href="{{ route('bab.show',$materi->bab) }}">Bab {{ $materi->bab->urutan }}</a> / {{ $materi->judul }}</div>
<article class="material-page">
    <div class="material-head">
        <span class="eyebrow">BAB {{ $materi->bab->urutan }} · MATERI</span>
        <h1>{{ $materi->judul }}</h1>
        <div class="lesson-meta">📘 {{ $materi->bab->judul }}</div>
    </div>

    <div class="lesson-progress" aria-label="Posisi materi">
        <div class="lesson-progress-top">
            <span>📖 Materi {{ $position }} dari {{ $totalMateriBab }}</span>
            <strong>{{ $materi->bab->judul }}</strong>
        </div>
        <div class="lesson-progress-track" aria-hidden="true"><span style="width: {{ $totalMateriBab ? ($position / $totalMateriBab) * 100 : 0 }}%"></span></div>
    </div>

    <div class="lesson-layout">
        <aside class="lesson-sidebar" aria-label="Daftar materi dalam bab">
            <div class="sidebar-title">
                <span>📚</span>
                <div><strong>Materi Bab Ini</strong><small>{{ $totalMateriBab }} materi</small></div>
            </div>
            <div class="sidebar-list">
                @foreach($materisBab as $item)
                    <a class="sidebar-item {{ $item->id === $materi->id ? 'active' : '' }}" href="{{ route('materi.show',$item) }}">
                        <span class="sidebar-number">{{ $loop->iteration }}</span>
                        <span>{{ $item->judul }}</span>
                        @if($item->id === $materi->id)<b>✓</b>@endif
                    </a>
                @endforeach
            </div>
        </aside>

        <div class="lesson-main">
    <div class="lesson-jump" aria-label="Navigasi cepat materi">
        <a href="#materi-isi">📖 Materi</a>
        @if($materi->videos->count())<a href="#video-pembelajaran">🎬 Video <span>{{ $materi->videos->count() }}</span></a>@endif
        @if($materi->referensi->count())<a href="#referensi">🔗 Referensi <span>{{ $materi->referensi->count() }}</span></a>@endif
    </div>

    <section id="materi-isi" class="lesson-content-section">
        <div class="content-section-label">📖 Materi Pembelajaran</div>
        <div class="isi">{!! nl2br(e($materi->isi)) !!}</div>
    </section>

    @if($materi->videos->count())
    <section id="video-pembelajaran" class="resource-section">
        <div class="resource-title"><span>🎬</span><div><span class="eyebrow">MEDIA</span><h2>Video Pembelajaran</h2><p class="resource-subtitle">Tonton video berikut untuk membantu memahami materi.</p></div></div>
        @foreach($materi->videos as $v)
        <div class="video-card">
            <div class="video-card-head">
                <div><span class="video-number">{{ $loop->iteration }}</span><h3>{{ $v->judul }}</h3></div>
            </div>
            @if($v->source_type === 'upload' && $v->file_video)
                <video controls preload="metadata" playsinline>
                    <source src="{{ asset('storage/'.$v->file_video) }}">
                    Browser Anda tidak mendukung pemutar video.
                </video>
                <span class="video-source-badge">📁 Video upload</span>
            @elseif(in_array($v->source_type, ['youtube','vimeo']) && $v->embedUrl())
                <div class="video-embed-wrap">
                    <iframe src="{{ $v->embedUrl() }}" title="{{ $v->judul }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>
                <span class="video-source-badge">🔗 {{ ucfirst($v->source_type) }}</span>
            @elseif($v->source_type === 'direct' && $v->video_url)
                <video controls preload="metadata" playsinline>
                    <source src="{{ $v->video_url }}">
                    Browser Anda tidak mendukung pemutar video atau link video tidak dapat diputar langsung.
                </video>
                <span class="video-source-badge">🔗 Direct video</span>
            @endif
            @if($v->deskripsi)<p>{{ $v->deskripsi }}</p>@endif
        </div>
        @endforeach
    </section>
    @else
    <section id="video-pembelajaran" class="resource-section empty-resource">
        <div class="empty-resource-icon">🎬</div>
        <div><span class="eyebrow">MEDIA</span><h2>Video Pembelajaran</h2><p>Video untuk materi ini belum tersedia. Silakan lanjutkan membaca materi di atas.</p></div>
    </section>
    @endif

    @if($materi->referensi->count())
    <section id="referensi" class="resource-section">
        <div class="resource-title"><span>🔗</span><div><span class="eyebrow">BELAJAR LEBIH LANJUT</span><h2>Referensi Tambahan</h2><p class="resource-subtitle">Sumber tambahan jika ingin mempelajari topik ini lebih lanjut.</p></div></div>
        <div class="refs">
            @foreach($materi->referensi as $r)
            @php
                $referenceUrl = $r->source_type === 'pdf' && $r->file_pdf
                    ? asset('storage/'.$r->file_pdf)
                    : $r->url;
            @endphp
            <a class="ref" target="_blank" rel="noopener noreferrer" href="{{ $referenceUrl }}">
                <span>{{ $r->source_type === 'pdf' ? '📄' : '🔗' }}</span>
                <span><strong>{{ $r->judul }}</strong><small>{{ $r->deskripsi ?: ($r->source_type === 'pdf' ? 'File PDF' : $r->url) }}</small></span>
                <b>↗</b>
            </a>
            @endforeach
        </div>
    </section>
    @else
    <section id="referensi" class="resource-section empty-resource">
        <div class="empty-resource-icon">🔗</div>
        <div><span class="eyebrow">BELAJAR LEBIH LANJUT</span><h2>Referensi Tambahan</h2><p>Belum ada referensi tambahan untuk materi ini.</p></div>
    </section>
    @endif

    <nav class="lesson-navigation" aria-label="Navigasi materi">
        @if($previous)
            <a href="{{ route('materi.show',$previous) }}" class="lesson-nav-card previous"><span>←</span><div><small>Materi sebelumnya</small><strong>{{ $previous->judul }}</strong></div></a>
        @else
            <span class="lesson-nav-card disabled"><span>←</span><div><small>Materi sebelumnya</small><strong>Ini materi pertama</strong></div></span>
        @endif
        @if($next)
            <a href="{{ route('materi.show',$next) }}" class="lesson-nav-card next"><div><small>Materi berikutnya</small><strong>{{ $next->judul }}</strong></div><span>→</span></a>
        @else
            <span class="lesson-nav-card disabled next"><div><small>Materi berikutnya</small><strong>Ini materi terakhir</strong></div><span>→</span></span>
        @endif
    </nav>
        </div>
    </div>
</article>
@endsection

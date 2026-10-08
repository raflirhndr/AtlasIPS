@extends('layouts.app')
@section('title','Bab '.$bab->urutan.' · '.$bab->judul.' · ATLAS IPS')
@section('content')
<div class="breadcrumb"><a href="{{ route('home') }}">ATLAS IPS</a><span>/</span><span>Bab {{ $bab->urutan }}</span></div>
<section class="page-banner">
    <span class="chapter-number">{{ str_pad($bab->urutan,2,'0',STR_PAD_LEFT) }}</span>
    <div>
        <span class="eyebrow">BAB {{ $bab->urutan }}</span>
        <h1>{{ $bab->judul }}</h1>
        <p>{{ $bab->deskripsi ?: 'Pelajari materi pada Bab ini secara bertahap.' }}</p>
        <div class="page-banner-meta">
            <span>📖 {{ $bab->materi->count() }} materi</span>
            <span>🎬 {{ $bab->materi->sum(fn($m) => $m->videos->count()) }} video</span>
            <span>🔗 {{ $bab->materi->sum(fn($m) => $m->referensi->count()) }} referensi</span>
        </div>
    </div>
</section>
<div class="section-heading"><div><span class="eyebrow">ISI BAB</span><h2>Materi Pembelajaran</h2></div></div>
<div class="material-list">
@forelse($bab->materi as $m)
    <a class="material-card" href="{{ route('materi.show',$m) }}"><span class="material-icon">📖</span><span><strong>{{ $m->judul }}</strong><small>Pelajari materi ini →</small></span><b>›</b></a>
@empty
    <div class="empty">Belum ada materi di Bab ini.</div>
@endforelse
</div>
@endsection

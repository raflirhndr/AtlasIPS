@extends('layouts.app')
@section('title','ATLAS IPS · Beranda')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">ATLAS IPS · SMP</span>
        <h1>Satu peta,<br><em>semua</em> ilmu sosial.</h1>
        <p>Materi pembelajaran IPS untuk SMP dikumpulkan dalam satu tempat agar mudah dipelajari, ditonton, dan dijelajahi.</p>
        <div class="hero-actions"><a class="primary hero-cta" href="#daftar-bab">Mulai Belajar →</a></div>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="hero-art-main"></div>
        <span>U</span><span>T</span><span>S</span><span>B</span>
    </div>
</section>

<div class="learning-stats" aria-label="Ringkasan pembelajaran">
    <div class="learning-stat"><span class="stat-icon">📚</span><div><strong>{{ $babs->count() }}</strong><small>Bab</small></div></div>
    <div class="learning-stat"><span class="stat-icon">📖</span><div><strong>{{ $babs->sum(fn($bab) => $bab->materi->count()) }}</strong><small>Materi</small></div></div>
    <div class="learning-stat"><span class="stat-icon">🎬</span><div><strong>{{ $babs->sum(fn($bab) => $bab->materi->sum(fn($m) => $m->videos->count())) }}</strong><small>Video</small></div></div>
    <div class="learning-stat"><span class="stat-icon">🔗</span><div><strong>{{ $babs->sum(fn($bab) => $bab->materi->sum(fn($m) => $m->referensi->count())) }}</strong><small>Referensi</small></div></div>
</div>

<div id="daftar-bab" class="section-heading"><div><span class="eyebrow">MATERI PEMBELAJARAN</span><h2>Pilih Bab untuk Mulai Belajar</h2></div><span class="count-pill">{{ $babs->count() }} Bab</span></div>
<div class="chapters">
@forelse($babs as $bab)
    <article class="chapter">
        <button class="toggle" type="button" aria-expanded="false">
            <span class="chapter-number">{{ str_pad($bab->urutan,2,'0',STR_PAD_LEFT) }}</span>
            <span class="chapter-info"><strong>{{ $bab->judul }}</strong><small>{{ $bab->materi->count() }} materi · {{ $bab->deskripsi ?: "Pelajari materi pada Bab ini." }}</small></span>
            <span class="chevron">⌄</span>
        </button>
        <div class="lessons">
            @foreach($bab->materi as $m)
                <a href="{{ route('materi.show',$m) }}"><span>📖 {{ $m->judul }}</span><b>›</b></a>
            @endforeach
            <a class="all" href="{{ route('bab.show',$bab) }}">Buka Bab {{ $bab->urutan }} →</a>
        </div>
    </article>
@empty
    <div class="empty">Belum ada Bab yang ditambahkan.</div>
@endforelse
</div>
@endsection

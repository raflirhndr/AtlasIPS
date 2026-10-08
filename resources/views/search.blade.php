@extends('layouts.app')
@section('title','Pencarian · ATLAS IPS')
@section('content')
<div class="breadcrumb"><a href="{{ route('home') }}">Beranda</a> / Pencarian</div>
<section class="page-banner search-banner">
    <div>
        <span class="eyebrow">PENCARIAN MATERI</span>
        <h1>Cari materi pembelajaran</h1>
        <p>Gunakan kata kunci seperti "algoritma", "pemrograman", atau "jaringan".</p>
    </div>
</section>
<form class="search-large" method="get" action="{{ route('search') }}">
    <input type="search" name="q" value="{{ $q }}" placeholder="Ketik kata kunci materi..." autofocus>
    <button class="primary">Cari</button>
</form>
@if($q !== '')
    <div class="section-heading"><div><span class="eyebrow">HASIL PENCARIAN</span><h2>{{ $materis->count() }} materi ditemukan</h2></div></div>
    <div class="material-list">
        @forelse($materis as $m)
            <a class="material-card" href="{{ route('materi.show', $m) }}">
                <span class="material-icon">📖</span>
                <span><strong>{{ $m->judul }}</strong><small>Bab {{ $m->bab->urutan }} — {{ $m->bab->judul }}</small></span>
                <b>›</b>
            </a>
        @empty
            <div class="empty">Materi dengan kata kunci <strong>“{{ $q }}”</strong> belum ditemukan.</div>
        @endforelse
    </div>
@else
    <div class="empty">Masukkan kata kunci untuk mencari materi.</div>
@endif
@endsection

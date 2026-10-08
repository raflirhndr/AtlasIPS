@extends('layouts.app')
@section('title','Login Admin · Belajar SMP')
@section('content')
<div class="auth-card">
    <div class="auth-icon">🔐</div>
    <h1>Login Admin</h1>
    <p>Masuk untuk mengelola Bab, materi, video, dan referensi pembelajaran.</p>
    <form method="post" action="{{ route('login.attempt') }}" class="form">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>
        <button class="primary">Masuk ke Dashboard</button>
    </form>
    <a class="back-link" href="{{ route('home') }}">← Kembali ke halaman pembelajaran</a>
</div>
@endsection

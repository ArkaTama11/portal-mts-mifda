@extends('layouts.app')

@section('title', $article->title . ' — ' . ($s['site_name'] ?? ''))

@section('content')
    <article class="max-w-3xl mx-auto px-4 py-10">
        <p class="text-xs font-bold uppercase tracking-wide text-kemenag">{{ $article->category }} &middot; {{ $article->published_at?->format('d M Y') }} &middot; {{ $article->author?->name }}</p>
        <h1 class="text-3xl font-extrabold mt-2 mb-5">{{ $article->title }}</h1>

        @if ($article->featured_image)
            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full rounded-2xl shadow mb-6">
        @endif

        <div class="prose-article bg-white rounded-2xl shadow p-6 sm:p-8">
            {!! $article->body !!}
        </div>

        <div class="mt-6 flex items-center gap-3">
            <span class="font-semibold">Bagikan:</span>
            <a class="bg-[#25d366] text-white font-semibold px-4 py-2 rounded-lg" target="_blank" rel="noopener"
               href="https://wa.me/?text={{ urlencode($article->title . ' — ' . url()->current()) }}">WhatsApp</a>
        </div>

        <p class="mt-8"><a href="/berita" class="font-semibold text-kemenag hover:underline">&larr; Kembali ke daftar berita</a></p>
    </article>
@endsection

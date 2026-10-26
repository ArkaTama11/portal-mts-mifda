@extends('layouts.app')

@section('title', 'Berita & Pengumuman — ' . ($s['site_name'] ?? ''))

@section('content')
    <section class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-extrabold text-kemenag mb-6">Berita & Pengumuman</h1>
        <div class="grid gap-6 md:grid-cols-3">
            @forelse ($articles as $article)
                <article class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
                    @if ($article->featured_image)
                        <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="h-44 w-full object-cover">
                    @endif
                    <div class="p-5 flex flex-col gap-2 grow">
                        <p class="text-xs font-bold uppercase tracking-wide text-kemenag">{{ $article->category }} &middot; {{ $article->published_at?->format('d M Y') }}</p>
                        <h2 class="font-bold leading-snug">
                            <a href="/berita/{{ $article->slug }}" class="hover:text-kemenag">{{ $article->title }}</a>
                        </h2>
                        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($article->excerpt ?? strip_tags($article->body), 130) }}</p>
                        <a href="/berita/{{ $article->slug }}" class="mt-auto font-semibold text-kemenag hover:underline">Baca selengkapnya &rarr;</a>
                    </div>
                </article>
            @empty
                <p class="text-gray-500">Belum ada artikel terbit.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $articles->links() }}</div>
    </section>
@endsection

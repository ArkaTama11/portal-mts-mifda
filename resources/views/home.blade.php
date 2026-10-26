@extends('layouts.app')

@section('content')
    @php
        // Sambutan dibaca dari settings (diubah lewat dashboard →
        // Pengaturan Situs). Bila nama/teks dikosongkan di sana,
        // tampilkan placeholder bawaan agar bagian ini tidak kosong.
        $headName = trim($s['head_name'] ?? '') !== '' ? $s['head_name'] : 'Kepala Madrasah';
        $sambutanText = trim($s['sambutan'] ?? '') !== '' ? $s['sambutan'] : \App\Support\SiteDefaults::sambutan();
    @endphp

    {{-- Hero --}}
    <section class="relative">
        <img src="/images/gedung.jpg" alt="Gedung {{ $s['site_name'] ?? '' }}" class="w-full h-[420px] object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[#075E2C]/90 via-[#0B7A3B]/40 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0">
            <div class="max-w-6xl mx-auto px-4 pb-10 text-white">
                <p class="font-arab text-2xl mb-2">المدرسة المتوسطة الإسلامية مفتاح الهدى دونجوس</p>
                <h1 class="text-3xl sm:text-5xl font-extrabold">{{ $s['site_name'] ?? '' }}</h1>
                <p class="mt-2 text-green-50 max-w-2xl">{{ $s['visi'] ?? '' }}</p>
                <div class="mt-5 flex gap-3">
                    <a href="/ppdb" class="bg-white text-kemenag font-bold px-5 py-2.5 rounded-lg">Info PPDB</a>
                    <a href="/berita" class="border border-white/70 text-white font-bold px-5 py-2.5 rounded-lg">Berita Terbaru</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Sambutan --}}
    <section class="max-w-6xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow p-6 sm:p-10 grid gap-8 md:grid-cols-[280px_1fr] items-center">
            <div class="mx-auto">
                <div class="rounded-t-[10rem] rounded-b-2xl overflow-hidden border-4 border-[#0B7A3B]/15 bg-[#e6f2e9]">
                    <img src="/images/kepala-madrasah.png" alt="Foto Kepala Madrasah" class="w-64 h-80 object-cover object-top">
                </div>
                <p class="text-center mt-3 font-bold">{{ $headName }}</p>
                <p class="text-center text-sm text-gray-500">Kepala Madrasah</p>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-kemenag mb-3">Sambutan Kepala Madrasah</h2>
                <p class="leading-8 text-gray-700">{{ $sambutanText }}</p>
            </div>
        </div>
    </section>

    {{-- Berita terbaru dari database --}}
    <section class="max-w-6xl mx-auto px-4 pb-4">
        <div class="flex items-end justify-between mb-5">
            <h2 class="text-2xl font-extrabold text-kemenag">Berita & Pengumuman Terbaru</h2>
            <a href="/berita" class="font-semibold text-kemenag hover:underline">Lihat semua &rarr;</a>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @forelse ($articles as $article)
                <article class="bg-white rounded-2xl shadow overflow-hidden flex flex-col">
                    @if ($article->featured_image)
                        <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="h-44 w-full object-cover">
                    @endif
                    <div class="p-5 flex flex-col gap-2 grow">
                        <p class="text-xs font-bold uppercase tracking-wide text-kemenag">{{ $article->category }} &middot; {{ $article->published_at?->format('d M Y') }}</p>
                        <h3 class="font-bold leading-snug">
                            <a href="/berita/{{ $article->slug }}" class="hover:text-kemenag">{{ $article->title }}</a>
                        </h3>
                        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($article->excerpt ?? strip_tags($article->body), 120) }}</p>
                    </div>
                </article>
            @empty
                <p class="text-gray-500">Belum ada artikel terbit.</p>
            @endforelse
        </div>
    </section>
@endsection

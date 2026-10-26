@extends('layouts.app')

@section('title', 'Kontak — ' . ($s['site_name'] ?? ''))

@section('content')
    <section class="max-w-4xl mx-auto px-4 py-10 space-y-8">
        <h1 class="text-3xl font-extrabold text-kemenag">Kontak</h1>

        <div class="bg-white rounded-2xl shadow p-6 sm:p-8 space-y-4">
            <div>
                <p class="font-bold">{{ $s['site_name'] ?? '' }}</p>
                <p class="text-gray-600">{{ $s['foundation'] ?? '' }}</p>
                <p class="text-gray-600">{{ $s['address'] ?? '' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $s['whatsapp_link'] ?? '#' }}" target="_blank" rel="noopener" class="bg-[#25d366] text-white font-semibold px-5 py-2.5 rounded-lg">WhatsApp {{ $s['whatsapp_number'] ?? '' }}</a>
                <a href="https://www.google.com/maps/search/{{ urlencode(($s['site_name'] ?? '') . ' ' . ($s['address'] ?? '')) }}" target="_blank" rel="noopener" class="bg-kemenag text-white font-semibold px-5 py-2.5 rounded-lg">Buka di Google Maps</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
            <h2 class="text-xl font-bold mb-3">Media Sosial</h2>
            <p class="text-gray-600 mb-4">Ikuti kegiatan madrasah melalui akun resmi {{ $s['social_username'] ?? '' }}:</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $s['instagram_url'] ?? '#' }}" target="_blank" rel="noopener" class="border border-green-200 font-semibold px-4 py-2 rounded-lg hover:bg-green-50">Instagram</a>
                <a href="{{ $s['facebook_url'] ?? '#' }}" target="_blank" rel="noopener" class="border border-green-200 font-semibold px-4 py-2 rounded-lg hover:bg-green-50">Facebook</a>
                <a href="{{ $s['tiktok_url'] ?? '#' }}" target="_blank" rel="noopener" class="border border-green-200 font-semibold px-4 py-2 rounded-lg hover:bg-green-50">TikTok</a>
            </div>
        </div>
    </section>
@endsection

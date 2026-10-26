@extends('layouts.app')

@section('title', 'Profil — ' . ($s['site_name'] ?? ''))

@section('content')
    <section class="max-w-4xl mx-auto px-4 py-10 space-y-8">
        <h1 class="text-3xl font-extrabold text-kemenag">Profil Madrasah</h1>

        <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
            <h2 class="text-xl font-bold mb-4">Identitas Madrasah</h2>
            <table class="w-full text-left">
                <tbody class="[&_td]:py-2.5 [&_td]:border-b [&_td]:border-green-50">
                    <tr><td class="font-semibold w-44">Nama Madrasah</td><td>{{ $s['site_name'] ?? '' }}</td></tr>
                    <tr><td class="font-semibold">Yayasan</td><td>{{ $s['foundation'] ?? '' }}</td></tr>
                    <tr><td class="font-semibold">NSM</td><td>{{ $s['nsm'] ?? '' }}</td></tr>
                    <tr><td class="font-semibold">NPSN</td><td>{{ $s['npsn'] ?? '' }}</td></tr>
                    <tr><td class="font-semibold">Alamat</td><td>{{ $s['address'] ?? '' }}</td></tr>
                    <tr><td class="font-semibold">WhatsApp</td><td>{{ $s['whatsapp_number'] ?? '' }}</td></tr>
                    <tr><td class="font-semibold">Media Sosial</td><td>{{ $s['social_username'] ?? '' }} (TikTok, Instagram, Facebook)</td></tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
            <h2 class="text-xl font-bold mb-3">Visi</h2>
            <p class="text-lg leading-8 font-semibold text-kemenag">&ldquo;{{ $s['visi'] ?? '' }}&rdquo;</p>

            <h2 class="text-xl font-bold mt-8 mb-3">Misi</h2>
            <ol class="list-decimal pl-6 space-y-2 leading-7">
                @foreach (array_filter(array_map('trim', explode("\n", $s['misi'] ?? ''))) as $misi)
                    <li>{{ $misi }}</li>
                @endforeach
            </ol>
        </div>
    </section>
@endsection

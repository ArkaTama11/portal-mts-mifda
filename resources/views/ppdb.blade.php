@extends('layouts.app')

@section('title', 'PPDB — ' . ($s['site_name'] ?? ''))

@section('content')
    @php
        // Seluruh isi halaman ini dibaca dari settings (diubah lewat
        // dashboard → Pengaturan → PPDB). Daftar terstruktur disimpan
        // sebagai JSON; bila belum pernah disimpan atau kosong, tampil
        // konten bawaan yang sama seperti versi awal halaman ini.
        $ppdbStatus = ($s['ppdb_status'] ?? 'ditutup') === 'dibuka' ? 'dibuka' : 'ditutup';
        $ppdbAnnouncement = trim($s['ppdb_announcement'] ?? '');
        $ppdbInfo = trim($s['ppdb_info'] ?? '');

        $waves = json_decode($s['ppdb_waves'] ?? '', true);
        $waves = is_array($waves)
            ? array_values(array_filter($waves, fn ($w) => is_array($w) && trim((string) ($w['name'] ?? '')) !== ''))
            : [];

        $steps = json_decode($s['ppdb_steps'] ?? '', true);
        $steps = is_array($steps) && $steps !== [] ? $steps : \App\Support\SiteDefaults::ppdbSteps();

        $requirements = json_decode($s['ppdb_requirements'] ?? '', true);
        $requirements = is_array($requirements) && $requirements !== [] ? $requirements : \App\Support\SiteDefaults::ppdbRequirements();

        $formatDate = function ($value) {
            if (! $value) {
                return '—';
            }
            try {
                return \Carbon\Carbon::parse($value)->locale('id')->translatedFormat('d F Y');
            } catch (\Throwable) {
                return (string) $value;
            }
        };
    @endphp

    <section class="max-w-4xl mx-auto px-4 py-10 space-y-8">
        <h1 class="text-3xl font-extrabold text-kemenag">PPDB — Penerimaan Peserta Didik Baru</h1>

        {{-- Lencana status + pengumuman --}}
        @if ($ppdbStatus === 'dibuka')
            <div class="bg-green-50 border border-green-200 text-green-900 rounded-2xl p-5">
                <span class="inline-block bg-kemenag text-white text-sm font-bold px-3 py-1 rounded-full">Pendaftaran Dibuka</span>
                @if ($ppdbAnnouncement !== '')
                    <p class="mt-3 leading-7">{{ $ppdbAnnouncement }}</p>
                @endif
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-5">
                <span class="inline-block bg-amber-500 text-white text-sm font-bold px-3 py-1 rounded-full">Pendaftaran Ditutup</span>
                <p class="mt-3 leading-7">{{ $ppdbAnnouncement !== '' ? $ppdbAnnouncement : \App\Support\SiteDefaults::ppdbAnnouncement() }}</p>
            </div>
        @endif

        {{-- Jadwal gelombang --}}
        <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
            <h2 class="text-xl font-bold mb-4">Jadwal Pendaftaran</h2>
            @if ($waves !== [])
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="[&_th]:py-2.5 [&_th]:border-b [&_th]:border-green-100 text-kemenag">
                                <th>Gelombang</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="[&_td]:py-2.5 [&_td]:border-b [&_td]:border-green-50">
                            @foreach ($waves as $wave)
                                <tr>
                                    <td class="font-semibold">{{ $wave['name'] }}</td>
                                    <td>{{ $formatDate($wave['start'] ?? null) }}</td>
                                    <td>{{ $formatDate($wave['end'] ?? null) }}</td>
                                    <td>{{ $wave['note'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 leading-7">Jadwal resmi gelombang pendaftaran akan diumumkan melalui halaman ini, halaman Berita &amp; Pengumuman, serta WhatsApp madrasah.</p>
            @endif
        </div>

        {{-- Alur pendaftaran --}}
        <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
            <h2 class="text-xl font-bold mb-4">Alur Pendaftaran</h2>
            <ol class="space-y-4">
                @foreach ($steps as $i => $step)
                    <li class="flex gap-4">
                        <span class="bg-kemenag text-white font-bold w-8 h-8 shrink-0 rounded-full flex items-center justify-center">{{ $i + 1 }}</span>
                        <p><strong>{{ is_array($step) ? ($step['title'] ?? '') : $step }}</strong>@if (is_array($step) && trim((string) ($step['description'] ?? '')) !== '') — {{ $step['description'] }}@endif</p>
                    </li>
                @endforeach
            </ol>
        </div>

        {{-- Berkas persyaratan --}}
        <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
            <h2 class="text-xl font-bold mb-4">Berkas Persyaratan</h2>
            <ul class="list-disc pl-6 space-y-2 leading-7">
                @foreach ($requirements as $requirement)
                    <li>{{ is_array($requirement) ? ($requirement['item'] ?? '') : $requirement }}</li>
                @endforeach
            </ul>
            <a href="{{ ($s['whatsapp_link'] ?? '#') . '?text=' . urlencode('Assalamu’alaikum, saya ingin bertanya tentang PPDB ' . ($s['site_name'] ?? '') . '.') }}" target="_blank" rel="noopener" class="inline-block mt-5 bg-[#25d366] text-white font-semibold px-4 py-2 rounded-lg">Tanya PPDB via WhatsApp</a>
        </div>

        @if ($ppdbInfo !== '')
            <div class="bg-white rounded-2xl shadow p-6 sm:p-8">
                <h2 class="text-xl font-bold mb-3">Informasi Tambahan</h2>
                <p class="leading-7 text-gray-700 whitespace-pre-line">{{ $ppdbInfo }}</p>
            </div>
        @endif
    </section>
@endsection

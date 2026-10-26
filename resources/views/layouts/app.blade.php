<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $s['site_name'] ?? 'MTs. Miftahul Huda Dongos')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Plus Jakarta Sans", system-ui, sans-serif; }
        .font-arab { font-family: "Amiri", serif; }
        .bg-kemenag { background-color: #0B7A3B; }
        .bg-kemenag-dark { background-color: #075E2C; }
        .text-kemenag { color: #0B7A3B; }
        .prose-article img { max-width: 100%; border-radius: 0.75rem; margin: 1rem 0; }
        .prose-article p { margin-bottom: 1rem; line-height: 1.8; }
    </style>
</head>
<body class="bg-[#f4f9f4] text-gray-800">
    <div class="bg-kemenag-dark text-white text-sm">
        <div class="max-w-6xl mx-auto px-4 py-1.5 flex justify-between gap-4">
            <span>{{ $s['foundation'] ?? '' }}</span>
            <span class="hidden sm:inline">{{ $s['address'] ?? '' }}</span>
        </div>
    </div>

    <header class="bg-white shadow sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-3">
            <a href="/" class="flex items-center gap-3">
                <img src="/images/logo.png" alt="Logo {{ $s['site_name'] ?? '' }}" class="h-12 w-auto">
            </a>
            <nav class="ml-auto flex flex-wrap gap-x-5 gap-y-1 font-semibold text-sm sm:text-base">
                <a href="/" class="hover:text-kemenag">Beranda</a>
                <a href="/profil" class="hover:text-kemenag">Profil</a>
                <a href="/berita" class="hover:text-kemenag">Berita</a>
                <a href="/ppdb" class="hover:text-kemenag">PPDB</a>
                <a href="/kontak" class="hover:text-kemenag">Kontak</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-kemenag-dark text-white mt-16">
        <div class="max-w-6xl mx-auto px-4 py-10 grid gap-8 md:grid-cols-3">
            <div>
                <img src="/images/logo.png" alt="Logo" class="h-14 w-auto bg-white rounded p-1 mb-3">
                <p class="font-bold">{{ $s['site_name'] ?? '' }}</p>
                <p class="text-sm text-green-100">{{ $s['foundation'] ?? '' }}</p>
                <p class="text-sm text-green-100">{{ $s['address'] ?? '' }}</p>
            </div>
            <div>
                <p class="font-bold mb-2">Menu</p>
                <ul class="space-y-1 text-sm text-green-100">
                    <li><a href="/profil" class="hover:text-white">Profil & Visi–Misi</a></li>
                    <li><a href="/berita" class="hover:text-white">Berita & Pengumuman</a></li>
                    <li><a href="/ppdb" class="hover:text-white">PPDB</a></li>
                    <li><a href="/kontak" class="hover:text-white">Kontak</a></li>
                </ul>
            </div>
            <div>
                <p class="font-bold mb-2">Hubungi Kami</p>
                <p class="text-sm text-green-100">WhatsApp: {{ $s['whatsapp_number'] ?? '' }}</p>
                <p class="text-sm text-green-100">Media sosial: {{ $s['social_username'] ?? '' }} (TikTok, Instagram, Facebook)</p>
                <a href="{{ $s['whatsapp_link'] ?? '#' }}" target="_blank" rel="noopener" class="inline-block mt-3 bg-[#25d366] text-white font-semibold px-4 py-2 rounded-lg">Chat WhatsApp</a>
            </div>
        </div>
        <div class="border-t border-green-800 text-center text-xs text-green-200 py-3">
            &copy; {{ date('Y') }} {{ $s['site_name'] ?? '' }}
        </div>
    </footer>
</body>
</html>

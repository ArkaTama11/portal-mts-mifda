<?php

use App\Models\Article;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

$settings = function (): array {
    return Setting::query()->pluck('value', 'key')->all();
};

Route::get('/', function () use ($settings) {
    return view('home', [
        's' => $settings(),
        'articles' => Article::published()->take(3)->get(),
    ]);
});

Route::get('/berita', function () use ($settings) {
    return view('berita.index', [
        's' => $settings(),
        'articles' => Article::published()->paginate(9),
    ]);
});

Route::get('/berita/{slug}', function (string $slug) use ($settings) {
    $article = Article::published()->where('slug', $slug)->firstOrFail();

    return view('berita.show', [
        's' => $settings(),
        'article' => $article,
    ]);
});

Route::get('/profil', function () use ($settings) {
    return view('profil', ['s' => $settings()]);
});

Route::get('/ppdb', function () use ($settings) {
    return view('ppdb', ['s' => $settings()]);
});

Route::get('/kontak', function () use ($settings) {
    return view('kontak', ['s' => $settings()]);
});

<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Izin peran ditegakkan di server lewat policy (bukan sekadar
        // tampilan form). Pendaftaran eksplisit agar tidak bergantung
        // pada auto-discovery.
        Gate::policy(Article::class, ArticlePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}

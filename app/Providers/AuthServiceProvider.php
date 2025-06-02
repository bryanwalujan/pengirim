<?php

namespace App\Providers;

use App\Models\SuratIjinSurvey;
use App\Models\SuratAktifKuliah;
use App\Policies\SuratIjinSurveyPolicy;
use Illuminate\Support\ServiceProvider;
use App\Policies\SuratAktifKuliahPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        SuratAktifKuliah::class => SuratAktifKuliahPolicy::class,
        SuratIjinSurvey::class => SuratIjinSurveyPolicy::class,
        // Tambahkan model dan policy lainnya di sini
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

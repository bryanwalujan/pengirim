<?php

namespace App\Providers;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\SuratPindah;
use App\Models\SuratIjinSurvey;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Policies\BeritaAcaraSeminarProposalPolicy;
use App\Policies\SuratPindahPolicy;
use App\Policies\SuratIjinSurveyPolicy;
use Illuminate\Support\ServiceProvider;
use App\Policies\SuratAktifKuliahPolicy;
use App\Policies\SuratCutiAkademikPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        SuratAktifKuliah::class => SuratAktifKuliahPolicy::class,
        SuratIjinSurvey::class => SuratIjinSurveyPolicy::class,
        SuratCutiAkademik::class => SuratCutiAkademikPolicy::class,
        SuratPindah::class => SuratPindahPolicy::class,
        BeritaAcaraSeminarProposal::class => BeritaAcaraSeminarProposalPolicy::class,
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

<?php

namespace App\Providers;

use App\Models\BeritaAcaraUjianHasil;
use App\Observers\BeritaAcaraUjianHasilObserver;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register helper secara manual
        $this->loadHelpersFrom(app_path('Helpers'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Set locale for Carbon to Indonesian
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'Indonesian');

        // Register observers
        BeritaAcaraUjianHasil::observe(BeritaAcaraUjianHasilObserver::class);
    }

    /**
     * Load helper files from a directory
     */
    protected function loadHelpersFrom($directory)
    {
        $helperFiles = glob($directory . '/*.php');

        foreach ($helperFiles as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
}

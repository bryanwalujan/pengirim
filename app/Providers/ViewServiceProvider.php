<?php

namespace App\Providers;

use App\Models\Service;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
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
        // Share services data dengan header layout
        View::composer('layouts.user.header', function ($view) {
            $services = Service::active()
                ->ordered()
                ->limit(6)
                ->get();

            $view->with('services', $services);
        });

        // Share services dengan homepage jika diperlukan
        View::composer('user.home.index', function ($view) {
            if (!$view->getData()['services'] ?? false) {
                $services = Service::active()
                    ->ordered()
                    ->get();

                $view->with('services', $services);
            }
        });
    }
}
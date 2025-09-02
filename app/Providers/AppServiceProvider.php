<?php

namespace App\Providers;

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

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('🔐 Reset Password E-Service')
                ->greeting('👋 Halo Mahasiswa UNIMA!')
                ->line(new HtmlString('<div style="text-align: center;"><img src="https://unima.ac.id/uploads/img_logo/1650416196421.png" alt="Logo E-Service" style="max-width: 120px; margin-bottom: 20px;"></div>'))
                ->line('Kami menerima permintaan untuk mengatur ulang password akun Anda.')
                ->action('Reset Password Sekarang', $url)
                ->line('Link ini hanya berlaku selama 60 menit.')
                ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
                ->salutation('Salam hormat, E-Service Teknik Informatika UNIMA');
        });

        //  Set locale for Carbon to Indonesian
        Carbon::setLocale('id');

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

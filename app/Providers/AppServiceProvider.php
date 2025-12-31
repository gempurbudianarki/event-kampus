<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

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
        // LOGIKA CERDAS:
        // Cek apakah aplikasi sedang berjalan di Server Production (Live)?
        if ($this->app->environment('production')) {
            // Kalau di Server: PAKSA HTTPS
            URL::forceScheme('https');
            
            // Paksa request object buat percaya kalau ini HTTPS
            // (Penting buat DOM Cloud / Cloudflare)
            $this->app['request']->server->set('HTTPS', 'on');
            
            if (property_exists($this->app['request'], 'server')) {
                 $this->app['request']->server->set('HTTP_X_FORWARDED_PROTO', 'https');
            }
        }
        // Kalau di Local (Laptop), kode di atas dicuekin. Aman.
    }
}
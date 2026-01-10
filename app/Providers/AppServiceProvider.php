<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\MimeTypeDetection\MimeTypeDetector;
use App\Services\SafeMimeTypeDetector;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override MIME type detector binding to use our safe detector
        // This prevents "Class finfo not found" errors on servers without fileinfo extension
        $this->app->bind(
            MimeTypeDetector::class,
            function ($app) {
                return new SafeMimeTypeDetector();
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

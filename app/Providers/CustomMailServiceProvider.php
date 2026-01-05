<?php

// app/Providers/CustomMailServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class CustomMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load the custom mail configuration
        Config::set('mailcustom.php', require config_path('mailcustom.php'));
    }
}

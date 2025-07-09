<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register helper classes
        $this->app->singleton('security.helper', function () {
            return new \App\Helpers\SecurityHelper();
        });
    }

    public function boot()
    {
        // Load helper functions
        if (file_exists(app_path('Helpers/functions.php'))) {
            require_once app_path('Helpers/functions.php');
        }
    }
}
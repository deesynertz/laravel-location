<?php

namespace Deesynertz\Location;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class LocateServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        // $this->loadViewsFrom(__DIR__.'/./../resources/views', 'views');
        // dd('location added');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublishables();
    }

    private function registerPublishables() 
    {
        $basePath = __DIR__;
        $arrPublishable = [
            'deesynertz-locate-migrations' => [
                "$basePath/publishable/database/migrations" => database_path('migrations'),
            ],
            'deesynertz-locate-config' => [
                "$basePath/publishable/config/locate.php" => config_path('locate.php'),
            ]
        ];

        foreach ($arrPublishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }
}

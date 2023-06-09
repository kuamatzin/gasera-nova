<?php

namespace Inovuz\MapKmz;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Inovuz\MapKmz\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //Nova::script('nova-google-maps_googlemaps', $this->gmapsScript());
        });
    }

    protected function gmapsScript()
    {
        return vsprintf(
            'https://maps.googleapis.com/maps/api/js?key=%s&language=%s',
            [
                'AIzaSyDb50wjGCHiWta4av3__VC3Rv-Hf0l3PZ8',
                config('nova-google-maps.language'),
            ]
        );
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'map-kmz')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/map-kmz')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

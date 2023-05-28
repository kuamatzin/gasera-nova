<?php

namespace Inovuz\FileKmz;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-google-maps_googlemaps', $this->gmapsScript());
            Nova::script('file-kmz', __DIR__ . '/../dist/js/field.js');
            Nova::style('file-kmz', __DIR__ . '/../dist/css/field.css');
        });
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

    protected function gmapsScript()
    {
        return vsprintf(
            'https://maps.googleapis.com/maps/api/js?key=%s&language=en&callback=initialize',
            [
                'AIzaSyAMsehXJFDs8U_p8iXS4sJVY386BUOBspk'
            ]
        );
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use App\Nova\Dashboards\Main;
use App\Nova\Dashboards\Sonora;
use CodencoDev\NovaGridSystem\NovaGridSystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Inovuz\CustomGridSystem\CustomGridSystem;
use Inovuz\MapKmz\MapKmz;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Oneduo\NovaFileManager\NovaFileManager;
use SimonHamp\LaravelNovaCsvImport\LaravelNovaCsvImport;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::footer(function ($request) {
        return Blade::render('
                B&L Advisers
            ');
        });
    }



    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, User::all()->pluck('email')->toArray());
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            Main::make()->showRefreshButton()->canSee(function($request) {
                if ($request->user()->role == 'admin' || $request->user()->role == 'cliente') {
                    return true;
                }
            }),
            Sonora::make()->showRefreshButton()->canSee(function($request) {
                if ($request->user()->role == 'admin' || $request->user()->role == 'cliente') {
                    return true;
                }
            })
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            //new NovaGridSystem
            //new LaravelNovaCsvImport,
            new MapKmz,
            new CustomGridSystem,
            NovaFileManager::make()->canSee(function($request) {
                if ($request->user()->role == 'admin') {
                    return true;
                }
            })
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \Laravel\Nova\Nova::$initialPath = '/resources/records';
    }
}

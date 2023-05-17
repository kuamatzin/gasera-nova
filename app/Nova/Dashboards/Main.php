<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AnuenciaTrabajosPreliminares;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Dashboard';
    }
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards(): array
    {
        return [
            new AnuenciaTrabajosPreliminares
        ];
    }
}

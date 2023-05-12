<?php

namespace Inovuz\CustomGridSystem;

use CodencoDev\NovaGridSystem\NovaGridSystem;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class CustomGridSystem extends NovaGridSystem
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('custom-grid-system', __DIR__.'/../dist/js/tool.js');
        Nova::style('custom-grid-system', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
    }
}

<?php

namespace Inovuz\BooleanSwitcher;

use Laravel\Nova\Fields\Boolean;

class BooleanSwitcher extends Boolean
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'boolean-switcher';
}

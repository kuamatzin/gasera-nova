<?php

namespace Acme\ColorPicker;

use Laravel\Nova\Fields\Text;

class ColorPicker extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'color-picker';
}

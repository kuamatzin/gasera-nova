<?php

namespace Inovuz\PanelEsteroids;

use Laravel\Nova\Panel;

class PanelEsteroids extends Panel
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'panel-esteroids';

    public $showOnDetail = true;

    public function showOnDetail($callback = true)
    {
        $this->showOnDetail = is_callable($callback) ? $callback() : $callback;

        return $this;
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'collapsable' => $this->collapsable,
            'collapsedByDefault' => $this->collapsedByDefault,
            'showOnDetail' => $this->showOnDetail,
            'component' => $this->component(),
            'name' => $this->name,
            'showToolbar' => $this->showToolbar,
            'limit' => $this->limit,
            'helpText' => $this->getHelpText(),
        ], $this->meta());
    }
}

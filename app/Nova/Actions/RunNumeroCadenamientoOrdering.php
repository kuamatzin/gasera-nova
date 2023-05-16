<?php

namespace App\Nova\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\Excel\Facades\Excel;

class RunNumeroCadenamientoOrdering extends Action
{
    public $name = 'Reordenar numeros de cadenamiento';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
            $path = $fields->consecutivos->storeAs('public', 'consecutivos.csv');
            $file = public_path('storage/consecutivos.csv');
            Excel::import(new \App\Imports\RecodsNumberFormat, $file);

            return Action::message('Numeros de cadenamiento reordenados correctamente');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            File::make('Archivo csv', 'consecutivos')->rules('required', 'mimes:csv'),
        ];
    }
}

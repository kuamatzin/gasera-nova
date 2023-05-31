<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateReport extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return ('Generar planillas de identificación');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        view()->share('records', $models);
        view()->share('download', true);
        view()->share('date', Carbon::now()->setTimezone('America/Mexico_City')->format('d-m-Y'));
        $pdf = Pdf::loadView('report');
        $pdf->save('testsave.pdf');

        return Action::download(url('testsave.pdf'), 'planilla_identificacion.pdf');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}

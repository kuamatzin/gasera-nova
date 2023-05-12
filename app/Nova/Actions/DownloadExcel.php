<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class DownloadExcel extends \Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel
{
    use InteractsWithQueue, Queueable;

    public $name = 'Descargar Excel';
}

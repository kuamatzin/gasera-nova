<?php

namespace App\Nova\Metrics;

use App\Models\Record;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Nemrutco\NovaGlobalFilter\GlobalFilterable;

class DocumentMetric extends Partition
{
    use GlobalFilterable;

    public function name()
    {
        $meta = $this->meta;
        return $meta['titulo'];
    }
    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $meta = $this->meta;

        $fase = $meta['fase'];
        $type = 'aus';

        $model = $this->globalFiltered($request, Record::class);

        if ($model) {
            $records = Record::all();
        } else {
            $records = Record::all();
        }
        $subido = 0;
        $no_subido = 0;
        foreach ($records as $record) {
            $dictamen_legal = $fase === '1' ? $record->dictamen_legal_fase_uno : $record->dictamen_legal_fase_dos;

            if ($dictamen_legal && isset($dictamen_legal[$type]) && $dictamen_legal[$type] !== '' && $dictamen_legal[$type] !== null) {
                $subido++;
            } else {
                $no_subido++;
            }
        }


        return $this->result([
            'No' => $no_subido,
            'Si' => $subido,
        ]);
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        //return now()->addMinutes(30);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'contrato-promesa-firmado';
    }
}

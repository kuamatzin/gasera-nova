<?php

namespace App\Nova\Metrics;

use App\Models\Record;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class DcitamenLegal extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $records = Record::all();
        $aceptado = 0;
        $no_aceptado = 0;
        foreach ($records as $record) {
            $dictamen_legal_fase_uno = $record->dictamen_legal_fase_uno;
            if ($dictamen_legal_fase_uno && isset($dictamen_legal_fase_uno['dictamen_legal_status']) && $dictamen_legal_fase_uno['dictamen_legal_status'] === 'aceptado') {
                $aceptado++;
            } else {
                $no_aceptado++;
            }
        }


        return $this->result([
            'No' => $no_aceptado,
            'Si' => $aceptado,
        ]);
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(30);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'dcitamen-legal';
    }
}

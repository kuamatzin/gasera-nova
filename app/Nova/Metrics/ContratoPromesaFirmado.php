<?php

namespace App\Nova\Metrics;

use App\Models\Record;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class DocumentMetric extends Partition
{

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
        $this->name('amskdmaskldmakslmdkl');
        $meta = $this->meta;
        $fase = $meta['fase'];
        $type = $meta['type'];

        $records = Record::all();
        $aceptado = 0;
        $no_aceptado = 0;
        foreach ($records as $record) {
            $dictamen_legal = $fase === '1' ? $record->dictamen_legal_fase_uno : $record->dictamen_legal_fase_dos;

            if ($dictamen_legal && isset($dictamen_legal[$type]) && $dictamen_legal[$type] === 'aceptado') {
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

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


    public function getFasesMetrics(NovaRequest $request, $fase, $type, $records)
    {
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
        ])->colors([
            'No' => '#EF8E00',
            'Si' => '#75BA25',
        ]);
    }

    public function getDocsMetrics(NovaRequest $request, $fase, $type, $records)
    {
        $subido = 0;
        $no_subido = 0;
        foreach ($records as $record) {
            if ($record->getRawOriginal('regimen_propiedad_inmueble') === 'pr') {
                $dictamen_legal = $record->documentacion;
                $type_pr = 'clg_pr';
                if ($dictamen_legal && isset($dictamen_legal[$type_pr]) && $dictamen_legal[$type_pr] !== '' && $dictamen_legal[$type_pr] !== null) {
                    $subido++;
                } else {
                    $no_subido++;
                }
            }
            if ($record->getRawOriginal('regimen_propiedad_inmueble') === 'pr' || $record->getRawOriginal('regimen_propiedad_inmueble') === 'pa') {
                $dictamen_legal = $record->documentacion;
                $type_pr = 'cvd_pa';
                if ($dictamen_legal && isset($dictamen_legal[$type_pr]) && $dictamen_legal[$type_pr] !== '' && $dictamen_legal[$type_pr] !== null) {
                    $subido++;
                } else {
                    $no_subido++;
                }
            }
        }


        return $this->result([
            'No' => $no_subido,
            'Si' => $subido,
        ])->colors([
            'No' => '#EF8E00',
            'Si' => '#75BA25',
        ]);
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
        $type = $meta['type'];
        $model = $this->globalFiltered($request, Record::class);

        if ($model) {
            if ($request->user()->entity === 'sonora') {
                $model->where('estado_inmueble', 'sonora');
            }

            if ($request->user()->entity === 'chihuahua') {
                $model->where('estado_inmueble', 'chihuahua');
            }

            $records = $model->get();
        } else {
            $records = Record::all();
        }

        if ($fase === 'docs') {
            return $this->getDocsMetrics($request, $fase, $type, $records);
        } else {
            return $this->getFasesMetrics($request, $fase, $type, $records);
        }
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
        return 'document-metric-' . $this->meta['fase'] . '-' . $this->meta['type'];
    }
}

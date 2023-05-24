<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;
use App\Nova\Filters\RecordState;
use App\Nova\Metrics\DocumentMetric;
use Nemrutco\NovaGlobalFilter\NovaGlobalFilter;

class Sonora extends Dashboard
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'Fase 2';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards(): array
    {
        $metrics = [
            [
                'titulo' => 'Aviso de interés',
                'fase' => '2',
                'type' => 'ain'
            ],
            [
                'titulo' => 'Notificación SENER',
                'fase' => '2',
                'type' => 'sener'
            ],
            [
                'titulo' => 'Notificación SEDATU',
                'fase' => '2',
                'type' => 'sedatu'
            ],
            [
                'titulo' => 'Anuencia de conformidad',
                'fase' => '2',
                'type' => 'adc'
            ],
            [
                'titulo' => 'Contrato firmado',
                'fase' => '2',
                'type' => 'cto'
            ],
            [
                'titulo' => 'Notificación de acuerdo cerrado a CRE',
                'fase' => '2',
                'type' => 'cre'
            ],
            [
                'titulo' => 'Notificación de acuerdo cerrado a SEDATU',
                'fase' => '2',
                'type' => 'csedatu'
            ],
            [
                'titulo' => 'Contrato de proceso de validación',
                'fase' => '2',
                'type' => 'cpv'
            ],
            [
                'titulo' => 'Contrato validado',
                'fase' => '2',
                'type' => 'cva'
            ],
            [
                'titulo' => 'Inscripción de contrato RAN/RPP',
                'fase' => '2',
                'type' => 'icr'
            ]
        ];
        return [
            new NovaGlobalFilter([
                new RecordState,
            ]),
            ...array_map(fn ($metric) => DocumentMetric::make()->withMeta(['titulo' => $metric['titulo'], 'fase' => $metric['fase'], 'type' => $metric['type']]), $metrics),
        ];
    }
}

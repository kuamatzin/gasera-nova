<?php

namespace App\Nova\Dashboards;

use App\Nova\Filters\RecordState;
use App\Nova\Metrics\DocumentMetric;
use Nemrutco\NovaGlobalFilter\NovaGlobalFilter;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Fase 1';
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
                'titulo' => 'Anuencia de trabajos preliminares',
                'fase' => '1',
                'type' => 'atp'
            ],
            [
                'titulo' => 'Anuencia cambio de uso de suelo',
                'fase' => '1',
                'type' => 'aus'
            ],
            /*
            [
                'titulo' => 'Cert. de Libertad de Gravamen/Constancia',
                'fase' => '1',
                'type' => 'clg2'
            ],
            */
            [
                'titulo' => 'Dictamen Legal',
                'fase' => '1',
                'type' => 'dictamen_legal'
            ],
            [
                'titulo' => 'Plano de afectación',
                'fase' => '1',
                'type' => 'paf'
            ],
            [
                'titulo' => 'Cuantificación de BDTS',
                'fase' => '1',
                'type' => 'cbdts'
            ],
            [
                'titulo' => 'Reporte Fotográfico BDTS',
                'fase' => '1',
                'type' => 'fbdts'
            ],
            [
                'titulo' => 'Contrato de Promesa Firmado',
                'fase' => '1',
                'type' => 'cpf'
            ],
            [
                'titulo' => 'CLG/CVD',
                'fase' => 'docs',
                'type' => 'clg_pr'
            ],
        ];


        return [
            new NovaGlobalFilter([
                new RecordState,
            ]),
            ...array_map(fn ($metric) => DocumentMetric::make()->withMeta(['titulo' => $metric['titulo'], 'fase' => $metric['fase'], 'type' => $metric['type']]), $metrics),
        ];
    }
}

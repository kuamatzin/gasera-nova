<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AnuenciaTrabajosPreliminares;
use App\Nova\Metrics\CertificadoLibertadGravamen;
use App\Nova\Metrics\CuantificacionDBTS;
use App\Nova\Metrics\DcitamenLegal;
use App\Nova\Metrics\DocumentMetric;
use App\Nova\Metrics\PlanoAfectacion;
use App\Nova\Metrics\ReporteFotograficoBDTS;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Todos';
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
                'type' => 'atp_status'
            ],
            [
                'titulo' => 'Anuencia cambio de uso de suelo',
                'fase' => '1',
                'type' => 'aus_status'
            ],
            [
                'titulo' => 'Certificado de Libertad de Gravamen/Constancia Vigencia de Derechos',
                'fase' => '1',
                'type' => 'clg_status'
            ],
            [
                'titulo' => 'Dictamen Legal',
                'fase' => '1',
                'type' => 'dictamen_legal_status'
            ],
            [
                'titulo' => 'Plano de afectación',
                'fase' => '1',
                'type' => 'paf_status'
            ],
            [
                'titulo' => 'Cuantificación de BDTS',
                'fase' => '1',
                'type' => 'cbdts_status'
            ],
            [
                'titulo' => 'Reporte Fotográfico BDTS',
                'fase' => '1',
                'type' => 'fbdts_status'
            ],
            [
                'titulo' => 'Contrato de Promesa Firmado',
                'fase' => '1',
                'type' => 'cpf_status'
            ]
        ];
        return [
            ...array_map(fn($metric) => DocumentMetric::make()->withMeta(['titulo' => $metric['titulo'], 'fase' => $metric['fase'], 'type' => $metric['type']]), $metrics),
        ];
    }
}

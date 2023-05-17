<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\AnuenciaTrabajosPreliminares;
use App\Nova\Metrics\CertificadoLibertadGravamen;
use App\Nova\Metrics\ContratoPromesaFirmado;
use App\Nova\Metrics\CuantificacionDBTS;
use App\Nova\Metrics\DcitamenLegal;
use App\Nova\Metrics\PlanoAfectacion;
use App\Nova\Metrics\ReporteFotograficoBDTS;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Dashboard';
    }
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards(): array
    {
        return [
            AnuenciaTrabajosPreliminares::make(),
            CertificadoLibertadGravamen::make(),
            ContratoPromesaFirmado::make(),
            CuantificacionDBTS::make(),
            DcitamenLegal::make(),
            PlanoAfectacion::make(),
            ReporteFotograficoBDTS::make(),
        ];
    }
}

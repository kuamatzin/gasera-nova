<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Record extends Resource
{
    public static function label()
    {
        return __('Expedientes');
    }


    public static function singularLabel()
    {
        return __('Expendiente');
    }

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Record>
     */
    public static $model = \App\Models\Record::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            new Panel('Propietario', $this->propietarioFields()),
            new Panel('Datos del inmueble a contratar', $this->inmuebleFields())
        ];
    }

    public function propietarioFields() {
        return [
            Text::make('Nombre del propietario y/o Dependencia', 'nombre_propietario_dependencia'),
            Text::make('Celular, Teléfono local o para recados', 'telefono_recados'),
            Text::make('Nombre del propietario y/o Dependencia', 'correo_electronico'),
            Text::make('Nombre del propietario y/o Dependencia', 'calificacion_propietario'),
            Text::make('Dirección del propietario para notificaciones (Debe incluir link de Google Street)', 'direccion_propietario_notificaciones'),
            Text::make('Código de Google Street', 'codigo_google_street'),
            Boolean::make('Representante Legal', 'representante_legal'),
            Text::make('Representante Legal', 'nombre_representante_legal')->nullable(),
            Text::make('Celular, Teléfono local para recados', 'telefono_recados_representante_legal')->nullable(),
            Text::make('Correo electrónico', 'correo_electronico_representante_legal')->nullable(),
            Text::make('Observaciones o comentarios', 'observaciones_representante_legal')->nullable(),
        ];
    }

    public function inmuebleFields() {
        return [
            Text::make('Dirección', 'direccion_inmueble'),
            Text::make('Poblado', 'poblado_inmueble'),
            Text::make('Municipio', 'municipio_inmueble'),
            Text::make('Estado', 'estado_inmueble'),
            Text::make('Régimen de propiedad', 'regimen_propiedad_inmueble'),
            Text::make('Uso de suelo', 'uso_suelo_inmueble'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}

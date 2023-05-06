<?php

namespace App\Nova;

use Inovuz\FileEsteroids\FileEsteroids;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Stepanenko3\NovaJson\JSON;

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
        'id', 'nombre_propietario_dependencia'
    ];

    /**
     * @param NovaRequest $request
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return match ($request->user()->role) {
            'admin' => $query,
            'abogado', 'coordinador', 'director', 'gestor' => $query->where('user_id', $request->user()->id),
            default => $query->where('user_id', $request->user()->id),
        };
    }

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
            BelongsTo::make('Usuario', 'user', User::class),
            Select::make('Estatus', 'status')->options([
                'progress' => 'En progreso',
                'revision' => 'Revisión',
                'completed' => 'Completado',
            ])->displayUsingLabels()->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            new Panel('Propietario', $this->propietarioFields()),
            new Panel('Datos del inmueble a contratar', $this->inmuebleFields()),
            new Panel('Superficies a contratar', $this->superficieFields()),
            new Panel('Mapa de afectación', $this->mapaFields()),
            new Panel('Documentación', $this->documentacionFields()),
        ];
    }

    public function validateEditionField(NovaRequest $request)
    {
        $allowed = ['admin'];
        if (in_array($request->user()->role, $allowed)) {
            return false;
        }
        return $this->status !== 'progress';
    }

    public function propietarioFields()
    {
        return [
            //FileEsteroids::make('Nombre del propietario y/o Dependencia', 'nombre_propietario_dependencia'),
            Text::make('Nombre del propietario y/o Dependencia', 'nombre_propietario_dependencia')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Celular, Teléfono local o para recados', 'telefono_recados')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Nombre del propietario y/o Dependencia', 'correo_electronico')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Nombre del propietario y/o Dependencia', 'calificacion_propietario')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Dirección del propietario para notificaciones (Debe incluir link de Google Street)', 'direccion_propietario_notificaciones')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Código de Google Street', 'codigo_google_street')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Boolean::make('Representante Legal', 'representante_legal')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Representante Legal', 'nombre_representante_legal')
                ->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Celular, Teléfono local para recados', 'telefono_recados_representante_legal')->nullable()->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Correo electrónico', 'correo_electronico_representante_legal')->nullable()->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Observaciones o comentarios', 'observaciones_representante_legal')->nullable()->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
        ];
    }

    public function inmuebleFields()
    {
        return [
            Text::make('Dirección', 'direccion_inmueble')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Select::make('Estado', 'estado_inmueble')->options([
                'chihuahua' => 'Chihuahua',
                'sonora' => 'Sonora',
            ])->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Select::make('Municipio', 'municipio_inmueble')->options([])->hide()->dependsOn(
                ['estado_inmueble'],
                function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->estado_inmueble === 'sonora') {
                        $field->show()->rules('required');
                        $field->options([
                            '10' => 'Bacerac',
                            '23' => 'Cumpas',
                            '31' => 'Hachinera',
                            '47' => 'Pitiquito',
                            '64' => 'Trincheras',
                            '22' => 'Cucurpe',
                            '58' => 'Santa Ana',
                            '06' => 'Arizpe',
                            '67' => 'Villa Hidalgo',
                        ]);
                    } else if ($formData->estado_inmueble === 'chihuahua') {
                        $field->show()->rules('required');
                        $field->options([
                            '01' => 'Villa Ahumada',
                            '10' => 'Buenaventura',
                            '13' => 'Casas Grandes',
                            '23' => 'Galeana',
                            '28' => 'Guadalupe',
                            '50' => 'Nuevo Casas Grandes',
                        ]);
                    } else {
                        $field->hide();
                    }
                }
            )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Poblado', 'poblado_inmueble')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Select::make('Régimen de propiedad', 'regimen_propiedad_inmueble')->options([
                'pr' => 'Propiedad privada',
                'ej' => 'Propiedad ejidal',
                'pa' => 'Parcela',
                'po' => 'Posesión',
                'ca' => 'Comunidad Agraria',
            ])->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Uso de suelo', 'uso_suelo_inmueble')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
        ];
    }

    public function superficieFields()
    {
        return [
            Select::make('Tipo de afectación', 'tipo_afectacion_superficie')->options([
                'servidumbre_voluntaria' => 'Servidumbre voluntaria',
                'estacion_medicion' => 'Estación de medición',
                'valvula_seccionamiento' => 'Válvula de seccionamiento',
            ])->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Superficie contratada m2', 'superficie_contratada_m2_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Superficie m2 franja de uso temporal', 'superficia_m2_franja_uso_temporal_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Superficie m2 FUTE', 'superficie_m2_fute_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Superficie total contratada m2', 'superficie_total_contratada_m2_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Km inicial', 'km_inicial_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Km final', 'km_final_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Longitud de afectación ML', 'longitud_afectacion_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Coordenada E', 'coordenada_e_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            Text::make('Coordenada N', 'coordenada_n_superficie')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
        ];
    }

    public function documentacionFields()
    {
        return array_merge($this->propiedadPrivadaFields(), $this->parcelaFields(), $this->ejidoFields());
    }

    public function propiedadPrivadaFields()
    {
        return [
            JSON::make('', 'documentacion', [
                ...$this->addHideFieldUntilOptionIsSelected('Identificación oficial', 'id_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Acta de nacimiento', 'an_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('CURP', 'curp_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('RFC', 'rfc_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Comprobante de domicilio', 'cd_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Escrituras o título de propiedad', 'es_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Plano de propiedad', 'pp_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Predial', 'pr_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Certificado libre de gravamen', 'cl_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Poder notarial para actos de dominio', 'pd_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Acta constitutiva', 'ac_pr', 'regimen_propiedad_inmueble', 'pr'),
                ...$this->addHideFieldUntilOptionIsSelected('Otro', 'ot_pr', 'regimen_propiedad_inmueble', 'pr'),
            ])
        ];
    }

    public function parcelaFields()
    {
        return [
            JSON::make('', 'documentacion', [
                ...$this->addHideFieldUntilOptionIsSelected('Identificación oficial', 'id_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('Acta de nacimiento', 'an_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('CURP', 'curp_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('RFC', 'rfc_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('Comprobante de domicilio', 'cd_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('Certificado parcelario', 'cp_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('Constancia de vigencia de derechos', 'cv_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('Poder notarial para actos de dominio', 'pn_pa', 'regimen_propiedad_inmueble', 'pa'),
                ...$this->addHideFieldUntilOptionIsSelected('Otro', 'ot_pa', 'regimen_propiedad_inmueble', 'pa'),
            ])
        ];
    }

    public function ejidoFields()
    {
        return [
            JSON::make('', 'documentacion', [
                ...$this->addHideFieldUntilOptionIsSelected('Acta de elección de los órganos', 'ae_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Resolución presidencial de dotación de tierras', 'rp_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Carpeta básica del ejido', 'cb_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Plano general del ejido', 'pg_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Identificación oficial de los representantes ejidales', 'id_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Padrón vigente de ejidatarios', 'pv_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Acta de asamblea autorizando el proyecto', 'aa_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Certificado parcelario con destino específico', 'aa_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Reglamento del ejido', 'aa_ej', 'regimen_propiedad_inmueble', 'ej'),
                ...$this->addHideFieldUntilOptionIsSelected('Otro', 'ot_ej', 'regimen_propiedad_inmueble', 'ej')
            ])
        ];
    }

    public function addHideFieldUntilOptionIsSelected($title, $value, $option, $optionSelected): array
    {
        $titleSection = Heading::make($title, $value)->hide()->dependsOn(
            [$option],
            function (Heading $field, NovaRequest $request, FormData $formData) use ($option, $optionSelected) {
                if ($formData[$option] === $optionSelected) {
                    $field->show();
                }
            }
        )->showOnDetail(function (NovaRequest $request, $resource) use ($option, $optionSelected) {
            return $this[$option] === $optionSelected;
        })->hideFromIndex();

        $file_field = FileEsteroids::make('', $value)->disk('public')->acceptedTypes('.pdf')->nullable()->hide()->dependsOn(
            [$option],
            function (FileEsteroids $field, NovaRequest $request, FormData $formData) use ($option, $optionSelected) {
                if ($formData[$option] === $optionSelected) {
                    $field->show();
                }
            }
        )->showOnDetail(function (NovaRequest $request, $resource) use ($option, $optionSelected) {
            return $this[$option] === $optionSelected;
        })->hideFromIndex();

        $select_field = Select::make('', $value . '_status')->options([
            'revision' => 'Revisión',
            'aceptado' => 'Aceptado',
            'rechazado' => 'Rechazado',
        ])->hide()->dependsOn(
            [$option],
            function (Select $field, NovaRequest $request, FormData $formData) use ($option, $optionSelected) {
                if ($formData[$option] === $optionSelected) {
                    $field->show();
                }
            }
        )->showOnDetail(function (NovaRequest $request, $resource) use ($option, $optionSelected) {
            return $this[$option] === $optionSelected;
        })->hideFromIndex();

        return [$titleSection, $file_field, $select_field];
    }

    public function mapaFields()
    {
        return [
            Text::make('Dirección', 'direccion_inmueble')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
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

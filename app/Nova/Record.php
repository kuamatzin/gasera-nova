<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\FormData;
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
            new Panel('Datos del inmueble a contratar', $this->inmuebleFields()),
            new Panel('Superficies a contratar', $this->superficieFields()),
            new Panel('Mapa de afectación', $this->mapaFields()),
            new Panel('Documentación', $this->documentacionFields())
        ];
    }

    public function propietarioFields()
    {
        return [
            Text::make('Nombre del propietario y/o Dependencia', 'nombre_propietario_dependencia'),
            Text::make('Celular, Teléfono local o para recados', 'telefono_recados'),
            Text::make('Nombre del propietario y/o Dependencia', 'correo_electronico'),
            Text::make('Nombre del propietario y/o Dependencia', 'calificacion_propietario')->hideFromIndex(),
            Text::make('Dirección del propietario para notificaciones (Debe incluir link de Google Street)', 'direccion_propietario_notificaciones')->hideFromIndex(),
            Text::make('Código de Google Street', 'codigo_google_street')->hideFromIndex(),
            Boolean::make('Representante Legal', 'representante_legal')->hideFromIndex(),
            Text::make('Representante Legal', 'nombre_representante_legal')
                ->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex(),
            Text::make('Celular, Teléfono local para recados', 'telefono_recados_representante_legal')->nullable()->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex(),
            Text::make('Correo electrónico', 'correo_electronico_representante_legal')->nullable()->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex(),
            Text::make('Observaciones o comentarios', 'observaciones_representante_legal')->nullable()->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex(),
        ];
    }

    public function inmuebleFields()
    {
        return [
            Text::make('Dirección', 'direccion_inmueble')->hideFromIndex(),
            Select::make('Estado', 'estado_inmueble')->options([
                'chihuahua' => 'Chihuahua',
                'sonora' => 'Sonora',
            ])->hideFromIndex(),
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
            )->hideFromIndex(),
            Text::make('Poblado', 'poblado_inmueble')->hideFromIndex(),
            Select::make('Régimen de propiedad', 'regimen_propiedad_inmueble')->options([
                'pr' => 'Propiedad privada',
                'ej' => 'Propiedad ejidal',
                'pa' => 'Parcela',
                'po' => 'Posesión',
                'ca' => 'Comunidad Agraria',
            ])->hideFromIndex(),
            Text::make('Uso de suelo', 'uso_suelo_inmueble')->hideFromIndex(),
        ];
    }

    public function superficieFields()
    {
        return [
            Select::make('Tipo de afectación', 'tipo_afectacion_superficie')->options([
                'servidumbre_voluntaria' => 'Servidumbre voluntaria',
                'estacion_medicion' => 'Estación de medición',
                'valvula_seccionamiento' => 'Válvula de seccionamiento',
            ])->hideFromIndex(),
            Text::make('Superficie contratada m2', 'superficie_contratada_m2_superficie')->hideFromIndex(),
            Text::make('Superficie m2 franja de uso temporal', 'superficia_m2_franja_uso_temporal_superficie')->hideFromIndex(),
            Text::make('Superficie m2 FUTE', 'superficie_m2_fute_superficie')->hideFromIndex(),
            Text::make('Superficie total contratada m2', 'superficie_total_contratada_m2_superficie')->hideFromIndex(),
            Text::make('Km inicial', 'km_inicial_superficie')->hideFromIndex(),
            Text::make('Km final', 'km_final_superficie')->hideFromIndex(),
            Text::make('Longitud de afectación ML', 'longitud_afectacion_superficie')->hideFromIndex(),
            Text::make('Coordenada E', 'coordenada_e_superficie')->hideFromIndex(),
            Text::make('Coordenada N', 'coordenada_n_superficie')->hideFromIndex(),
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
                $this->addHideFieldUntilOptionIsSelected(File::make('Identificación oficial', 'id_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Acta de nacimiento', 'an_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('CURP', 'curp_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('RFC', 'rfc_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Comprobante de domicilio', 'cd_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Escrituras o título de propiedad', 'es_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Plano de propiedad', 'pp_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Predial', 'pr_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Certificado libre de gravamen', 'cl_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Poder notarial para actos de dominio', 'pd_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Acta constitutiva', 'ac_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Otro', 'ot_pr')->disk('public'), 'regimen_propiedad_inmueble', 'pr'),
            ])
        ];
    }

    public function parcelaFields()
    {
        return [
            JSON::make('', 'documentacion', [
                $this->addHideFieldUntilOptionIsSelected(File::make('Identificación oficial', 'id_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Acta de nacimiento', 'an_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('CURP', 'curp_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('RFC', 'rfc_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Comprobante de domicilio', 'cd_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Certificado parcelario', 'cp_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Constancia de vigencia de derechos', 'cv_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Poder notarial para actos de dominio', 'pn_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Otro', 'ot_pa')->disk('public'), 'regimen_propiedad_inmueble', 'pa'),
            ])
        ];
    }

    public function ejidoFields()
    {
        return [
            JSON::make('', 'documentacion', [
                $this->addHideFieldUntilOptionIsSelected(File::make('Acta de elección de los órganos', 'ae_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Resolución presidencial de dotación de tierras', 'rp_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Carpeta básica del ejido', 'cb_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Plano general del ejido', 'pg_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Identificación oficial de los representantes ejidales', 'id_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Padrón vigente de ejidatarios', 'pv_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Acta de asamblea autorizando el proyecto', 'aa_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Certificado parcelario con destino específico', 'aa_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Reglamento del ejido', 'aa_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej'),
                $this->addHideFieldUntilOptionIsSelected(File::make('Otro', 'ot_ej')->disk('public'), 'regimen_propiedad_inmueble', 'ej')
            ])
        ];
    }

    public function addHideFieldUntilOptionIsSelected($field, $option, $optionSelected)
    {
        return $field->acceptedTypes('.pdf')->nullable()->hide()->dependsOn(
            [$option],
            function (File $field, NovaRequest $request, FormData $formData) use ($option, $optionSelected) {
                if ($formData[$option] === $optionSelected) {
                    $field->show();
                }
            }
        )->showOnDetail(function (NovaRequest $request, $resource) use ($option, $optionSelected) {
            return $this[$option] === $optionSelected;
        });
    }

    public function mapaFields()
    {
        return [
            Text::make('Dirección', 'direccion_inmueble')->hideFromIndex(),
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

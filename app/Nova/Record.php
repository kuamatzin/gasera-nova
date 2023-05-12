<?php

namespace App\Nova;

use App\Nova\Filters\RecordState;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;
use Inovuz\BooleanSwitcher\BooleanSwitcher;
use Inovuz\FileEsteroids\FileEsteroids;
use Inovuz\FileKmz\FileKmz;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use NormanHuth\NovaRadioField\Radio;
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

    public static $indexDefaultOrder = [
        'id' => 'asc'
    ];


    /**
     * @param NovaRequest $request
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->entity === 'chihuahua') {
            return $query->where('estado_inmueble', 'CHIHUAHUA');
        }

        if ($request->user()->entity === 'sonora') {
            return $query->where('estado_inmueble', 'SONORA');
        }

        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];

            return match ($request->user()->role) {
                'admin', 'cliente' => $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder)),
                'abogado', 'coordinador', 'director', 'gestor' => $query->where('user_id', $request->user()->id)->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder)),
                default => $query->where('user_id', $request->user()->id)->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder)),
            };
        }

        return match ($request->user()->role) {
            'admin', 'cliente' => $query,
            'abogado', 'coordinador', 'director', 'gestor' => $query->where('user_id', $request->user()->id),
            default => $query->where('user_id', $request->user()->id),
        };
    }

    public function generateNumeroExpediente($request)
    {
        $municipios_chihuahua = [
            'villa ahumada' => '001',
            'buenaventura' => '010',
            'casas grandes' => '013',
            'galeana' => '023',
            'guadalupe' => '028',
            'nuevo casas grandes' => '050',
        ];

        $municipios_sonora = [
            'bacerac' => '010',
            'cumpas' => '023',
            'hachinerea' => '031',
            'pitiquito' => '047',
            'trincheras' => '064',
            'cucurpe' => '022',
            'santa ana' => '058',
            'arizpe' => '006',
            'villa hidalgo' => '067',
        ];

        $regimen_propiedad = [
            'propiedad' => 'P',
        ];

        $record = \App\Models\Record::find($request->resourceId);
        if (!$record) {
            return '';
        }
        $estado = $record->estado_inmueble;
        $estado = strtolower($estado) === 'sonora' ? 'SN' : 'CH';

        $municipio = strtolower($record->municipio_inmueble);
        if ($estado === 'SN') {
            $municipio = $municipios_sonora[$municipio];
        } else {
            $municipio = $municipios_chihuahua[$municipio];
        }

        $propiedad = strtolower($record->regimen_propiedad_inmueble);


        return 'SM-' . $estado . '-' . $municipio;
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
            ID::make('Número de cadenamiento', 'id')->sortable(),
            Text::make('Número de expediente', 'numero_expediente')->readonly(false)->size('w-1/3'),
            BelongsTo::make('Gestor', 'user', User::class)->hideFromIndex(fn() => Auth::user()->role === 'cliente')->size('w-1/3'),
            Select::make('Estatus', 'status')->options(function () {
                if (Auth::user()->role === 'admin') {
                    return [
                        'progress' => 'En progreso',
                        'revision' => 'Revisión',
                        'completed' => 'Completado',
                    ];
                }

                return [
                    'progress' => 'En progreso',
                    'revision' => 'Revisión',
                ];
            })->displayUsingLabels()->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            new Panel('Propietario', $this->propietarioFields()),
            new Panel('Datos del inmueble a contratar', $this->inmuebleFields()),
            new Panel('Superficies a contratar', $this->superficieFields()),
            new Panel('Mapa de afectación', $this->mapaFields()),
            new Panel('Documentación', $this->documentacionFields()),
            new Panel('Dictamen Legal', $this->dictamenLegalFields()),
            new Panel('Fase 1', $this->faseUnoFields()),
            new Panel('Fase 2', $this->faseDosFields()),
            //new Panel('Dictamen Legal', $this->dictamenLegalFields()),
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

    public function readOnlyFunction(NovaRequest $request, $options)
    {
        if (in_array($request->user()->role, $options['readonly'])) {
            return false;
        }
        return $this->status !== 'progress';
    }

    public function validateDictamenLegal(NovaRequest $request)
    {
        $allowed = ['admin', 'abogado'];
        if (in_array($request->user()->role, $allowed)) {
            return false;
        }
        return $this->status !== 'progress';
    }

    public function representateLegalConfig($field)
    {
        return $field->hide()
            ->dependsOn(
                ['representante_legal'],
                function (Text $field, NovaRequest $request, FormData $formData) {
                    if ($formData->representante_legal) {
                        $field->show();
                    }
                }
            )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3');
    }

    public function propietarioFields()
    {
        return [
            Text::make('Planilla de identificación', 'nombre_propietario_dependencia')->readonly(function (NovaRequest $request) {
                $allowed = ['admin'];
                if (in_array($request->user()->role, $allowed)) {
                    return false;
                }
                if ($request->user()->role === 'abogado') {
                    return true;
                }
                return $this->status !== 'progress';
            })->size('w-1/3'),
            Text::make('Celular, Teléfono local o para recados', 'telefono_recados')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Correo electrónico', 'correo_electronico')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Select::make('Calificación del propietario', 'calificacion_propietario')->options([
                'a' => 'a',
                'b' => 'b',
                'c' => 'c',
            ])->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Dirección del propietario para notificaciones (Debe incluir link de Google Street)', 'direccion_propietario_notificaciones')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Código de Google Street', 'codigo_google_street')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            BooleanSwitcher::make('Representante Legal', 'representante_legal')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-full'),
            $this->representateLegalConfig(Text::make('Representante Legal', 'nombre_representante_legal')),
            $this->representateLegalConfig(Text::make('Celular, Teléfono local para recados', 'telefono_recados_representante_legal')),
            $this->representateLegalConfig(Text::make('Correo electrónico', 'correo_electronico_representante_legal')),
            Textarea::make('Observaciones o comentarios', 'observaciones_representante_legal')->hide()
                ->dependsOn(
                    ['representante_legal'],
                    function (Textarea $field, NovaRequest $request, FormData $formData) {
                        if ($formData->representante_legal) {
                            $field->show()->rules('required');
                        }
                    }
                )->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-full'),
            //$this->representateLegalConfig(Text::make('Observaciones o comentarios', 'observaciones_representante_legal')),
        ];
    }

    public function inmuebleFields()
    {
        return [
            Text::make('Dirección', 'direccion_inmueble')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Ejido', 'ejido_inmueble')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Select::make('Estado', 'estado_inmueble')->options([
                'chihuahua' => 'Chihuahua',
                'sonora' => 'Sonora',
            ])->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Select::make('Municipio', 'municipio_inmueble')->options([])->hide()->dependsOn(
                ['estado_inmueble'],
                function (Select $field, NovaRequest $request, FormData $formData) {
                    if ($formData->estado_inmueble === 'sonora') {
                        $field->show()->rules('required');
                        $field->options([
                            '010' => '010 - Bacerac',
                            '023' => '023 - Cumpas',
                            '031' => '031 - Hachinera',
                            '047' => '047 - Pitiquito',
                            '064' => '064 - Trincheras',
                            '022' => '022 - Cucurpe',
                            '058' => '058 - Santa Ana',
                            '006' => '006 - Arizpe',
                            '067' => '067 - Villa Hidalgo',
                        ]);
                    } else if ($formData->estado_inmueble === 'chihuahua') {
                        $field->show()->rules('required');
                        $field->options([
                            '001' => '001 - Villa Ahumada',
                            '010' => '010 - Buenaventura',
                            '013' => '013 - Casas Grandes',
                            '023' => '023 - Galeana',
                            '028' => '028 - Guadalupe',
                            '050' => '050 - Nuevo Casas Grandes',
                        ]);
                    } else {
                        $field->hide();
                    }
                }
            )->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Poblado', 'poblado_inmueble')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Select::make('Régimen de propiedad', 'regimen_propiedad_inmueble')->options([
                'pr' => 'Propiedad privada',
                'ej' => 'Propiedad ejidal',
                'pa' => 'Parcela',
                'po' => 'Posesión',
                'ca' => 'Comunidad Agraria',
            ])->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Uso de suelo', 'uso_suelo_inmueble')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
        ];
    }

    public function superficieFields()
    {
        return [
            Select::make('Tipo de afectación', 'tipo_afectacion_superficie')->options([
                'servidumbre_voluntaria' => 'Servidumbre voluntaria',
                'estacion_medicion' => 'Estación de medición',
                'valvula_seccionamiento' => 'Válvula de seccionamiento',
            ])->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Superficie contratada m2', 'superficie_contratada_m2_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Superficie m2 franja de uso temporal', 'superficia_m2_franja_uso_temporal_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Superficie m2 FUTE', 'superficie_m2_fute_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Superficie total contratada m2', 'superficie_total_contratada_m2_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Km inicial', 'km_inicial_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Km final', 'km_final_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Longitud de afectación ML', 'longitud_afectacion_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Coordenada E', 'coordenada_e_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
            Text::make('Coordenada N', 'coordenada_n_superficie')->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->hideFromIndex()->readonly(fn(NovaRequest $r) => $this->validateEditionField($r))->size('w-1/3'),
        ];
    }

    public function documentacionFields(): array
    {
        return array_merge($this->propiedadPrivadaFields(), $this->parcelaFields(), $this->ejidoFields());
    }

    public function faseUnoFields(): array
    {
        return [
            JSON::make('', 'dictamen_legal_fase_uno', [
                BooleanSwitcher::make('Propietario localizado', 'propietario_localizado')->hideFromIndex()->size('w-full'),
                ...$this->fieldFileFasesDictamenLegal('Anuencia de trabajos preliminares', 'anuencia_trabajos_preliminares'),
                ...$this->fieldFileFasesDictamenLegal('Anuencia cambio de uso de suelo', 'anuencia_cambio_uso_suelo'),
                BooleanSwitcher::make('Obtención de Documentación Legal', 'obtenicion_documentacion_legal')->hideFromIndex(),
                ...$this->fieldFileFasesDictamenLegal('Certificado de Libertad de Gravamen/Constancia Vigencia de Derechos ', 'certificado_libertad_gravamen'),
                ...$this->fieldFileFasesDictamenLegal('Dictamen Legal', 'dictamen_legal'),
                ...$this->fieldFileFasesDictamenLegal('Plano de afectación', 'plano_afectacion'),
                ...$this->fieldFileFasesDictamenLegal('Cuantificación de BDTS', 'cuantificacion_bdts'),
                ...$this->fieldFileFasesDictamenLegal('Reporte Fotográfico BDTS', 'reporte_fotografico_bdts'),
                ...$this->fieldFileFasesDictamenLegal('Contrato de Promesa Firmado', 'contrato_promesa_firmado'),
            ])
        ];
    }

    public function faseDosFields(): array
    {
        return [
            JSON::make('', 'dictamen_legal_fase_dos', [
                ...$this->fieldFileFasesDictamenLegal('Aviso de interés', 'aviso_interes'),
                ...$this->fieldFileFasesDictamenLegal('Notificación SENER', 'notificacion_sener'),
                ...$this->fieldFileFasesDictamenLegal('Notificación SEDATU', 'notificacion_sedatu'),
                ...$this->fieldFileFasesDictamenLegal('Anuencia de conformidad', 'anuencia_conformidad'),
                ...$this->fieldFileFasesDictamenLegal('Contrato firmado', 'contrato_firmaddo'),
                ...$this->fieldFileFasesDictamenLegal('Notificación de acuerdo cerrado a CRE', 'notificacion_acuerdo_cerrado_cre'),
                ...$this->fieldFileFasesDictamenLegal('Notificación de acuerdo cerrado a SEDATU', 'notificacion_acuerdo_cerrado_sedatu'),
                ...$this->fieldFileFasesDictamenLegal('Contrato de proceso de validación', 'contrato_proceso_validacion'),
                ...$this->fieldFileFasesDictamenLegal('Contrato validado', 'contrato_validado'),
                ...$this->fieldFileFasesDictamenLegal('Inscripción de contrato RAN/RPP', 'inscripcion_contrato_ran_rpp'),
            ])
        ];
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

    public function dictamenLegalFields()
    {
        return [
            BooleanSwitcher::make('Documentación completa para firmar contrato', 'documentación_completa_firmar_contrato')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/2'),
            BooleanSwitcher::make('Cuenta con CLG/CVD', 'clg_cvd')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/2'),
            Textarea::make('Dictamen Legal', 'dictamen_legal')->rows(35)->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-full'),
            Select::make('Convenio de promesa', 'convenio_promesa')->options([
                'Si' => 'Si',
                'No' => 'No',
                'n/a' => 'N/A'
            ])->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            BooleanSwitcher::make('Contrato definitivo', 'contrato_definitivo')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            BooleanSwitcher::make('Convenio sujeto a condición', 'convenio_sujeto_condicion')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            BooleanSwitcher::make('Indique si se identificó alguna inconsistencia importante', 'identificacion_inconsistencia_importante')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Text::make('Descripción', 'identificacion_inconsistencia_importante_contenido')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            BooleanSwitcher::make('Acción legal que se tendría que realizar para regularizar la Legal Tenencia de la Tierra', 'accion_legal_regularizar_tierra')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Text::make('Descripción', 'accion_legal_regularizar_tierra_contenido')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Select::make('Clasificación de la contingencia de acuerdo a la inconsistencia detectada', 'clasificacion_contingencia_detectada')->options([
                'simple' => 'Simple de resolver',
                'requiere_accion_legal_gasto' => 'Requiere acción legal, gasto, etc',
                'complejo_costoso_tiempo_extenso' => 'Complejo, costoso, tiempo extenso para resolver',
            ])->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Number::make('Meses para resolver la contingencia', 'meses_regularizar_contingencia')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Number::make('Monto aproximado (en pesos)', 'monto_aproximado')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Text::make('Terminos y ocndiciones para la celebración del Contrato Respectivo', 'terminos_condiciones_celebracion_contrato')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Text::make('Abogado emitió dictamen', 'abogado_emitio_dictamen')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
            Date::make('Fecha', 'fecha_dictamen')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'abogado')->readonly(fn(NovaRequest $r) => $this->validateDictamenLegal($r))->size('w-1/3'),
        ];
    }

    public function fieldFileFasesDictamenLegal($title, $value): array
    {
        $file_field = FileEsteroids::make('', $value)->storeAs(function ($request) use ($value) {
            return $request->numero_expediente . '.pdf';
        })->disk('public')->acceptedTypes('.pdf')->nullable()->hideFromIndex()->size('w-1/4');

        $select_field = Select::make('', $value . '_status')->options(function () {
            if (Auth::user()->role === 'admin') {
                return [
                    'revision' => 'Revisión',
                    'aceptado' => 'Aceptado',
                    'rechazado' => 'Rechazado',
                ];
            }

            return [
                'revision' => 'Revisión',
            ];
        })->hideFromIndex()->size('w-1/4');;

        return [$file_field, $select_field];
    }

    public function addHideFieldUntilOptionIsSelected($title, $value, $option, $optionSelected): array
    {

        $file_field = FileEsteroids::make($title, $value)->disk('public')->acceptedTypes('.pdf')->nullable()->hide()->dependsOn(
            [$option],
            function (FileEsteroids $field, NovaRequest $request, FormData $formData) use ($option, $optionSelected) {
                if ($formData[$option] === $optionSelected) {
                    $field->show();
                }
            }
        )->showOnDetail(function (NovaRequest $request, $resource) use ($option, $optionSelected) {
            return $this[$option] === $optionSelected;
        })->hideFromIndex()->size('w-1/4');

        $select_field = Radio::make('', $value . '_status')->options(function () {
            if (Auth::user()->role === 'admin') {
                return [
                    'revision' => 'Revisión',
                    'aceptado' => 'Aceptado',
                    'rechazado' => 'Rechazado',
                ];
            }

            return [
                'revision' => 'Revisión',
            ];
        })->hide()->dependsOn(
            [$option],
            function (Select $field, NovaRequest $request, FormData $formData) use ($option, $optionSelected) {
                if ($formData[$option] === $optionSelected) {
                    if (Auth::user()->role === 'admin') {
                        $field->show();
                    }
                }
            }
        )->showOnDetail(function (NovaRequest $request, $resource) use ($option, $optionSelected) {
            return $this[$option] === $optionSelected;
        })->hideFromIndex()->size('w-1/4');

        return [$file_field, $select_field];
    }

    public function mapaFields()
    {
        return [
            //Text::make('Dirección', 'direccion_inmueble')->hideFromIndex()->showOnUpdating(fn() => Auth::user()->role === 'admin' || Auth::user()->role === 'gestor')->readonly(fn(NovaRequest $r) => $this->validateEditionField($r)),
            FileEsteroids::make('Dirección', 'direccion_inmueble')->size('w-full'),
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
        return [
            new RecordState,
        ];
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
        return [
            (new \App\Nova\Actions\DownloadExcel)->allFields()->withFilename('expedientes-' . time() . '.xlsx'),
        ];
    }
}

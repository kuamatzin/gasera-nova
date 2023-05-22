<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    @if ($download)
        <link rel="stylesheet" href="{{ public_path('/css/app.css') }}">
    @else
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @endif

    <style>
        @page {
            margin: 45px 5px;
        }

        header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: 0px;
            height: 130px;

            /** Extra personal styles **/
            background-color: #03a9f4;
            color: white;
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 130px;

            /** Extra personal styles **/
            background-color: #03a9f4;
            color: white;
            text-align: center;
            line-height: 35px;
        }

        .page-break {
            page-break-after: always;
        }

        .mt-7 {
            margin-top: 5rem !important;
        }
    </style>
</head>


<body>

    <main>
        @foreach ($records as $key => $record)
            <div class="container">

                <h2 class="text-center">Reporte</h2>

                <table class="table table-bordered">
                    <tr>
                        <td>Número cadenamiento</td>
                        <td>
                            <strong>{{ $record->numero_cadenamiento }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Número expediente</td>
                        <td>
                            <strong>{{ $record->numero_expediente }}</strong>
                        </td>
                    </tr>
                </table>

                <h4>Planilla de Identificación</h4>

                <table class="mt-2 table table-bordered">
                    <tbody>
                        <tr>
                            <td>Nombre propietario o dependencia</td>
                            <td>{{ $record->nombre_propietario_dependencia }}</td>
                        </tr>
                        <tr>
                            <td>Teléfono recados</td>
                            <td>{{ $record->telefono_recados }}</td>
                        </tr>
                        <tr>
                            <td>Correo electrónico</td>
                            <td>{{ $record->correo_electronico }}</td>
                        </tr>
                        <tr>
                            <td>Calificación propietario</td>
                            <td>{{ $record->calificacion_propietario }}</td>
                        </tr>
                        <tr>
                            <td>Dirección notificaciones</td>
                            <td>{{ $record->direccion_propietario_notificaciones }}</td>
                        </tr>
                        <tr>
                            <td>Representante legal</td>
                            <td>{{ $record->representante_legal }}</td>
                        </tr>
                        <tr>
                            <td>Nombre representante legal</td>
                            <td>{{ $record->nombre_representante_legal }}</td>
                        </tr>
                        <tr>
                            <td>Teléfono recados</td>
                            <td>{{ $record->telefono_recados_representante_legal }}</td>
                        </tr>
                        <tr>
                            <td>Correo electrónico</td>
                            <td>{{ $record->correo_electronico_representante_legal }}</td>
                        </tr>
                        <tr>
                            <td>Observaciones</td>
                            <td>{{ $record->observaciones_representante_legal }}</td>
                        </tr>
                    </tbody>
                </table>

                <h4>Inmueble</h4>

                <table class="mt-2 table table-bordered">
                    <tbody>
                        <tr>
                            <td>Dirección inmueble</td>
                            <td>{{ $record->direccion_inmueble }}</td>
                        </tr>
                        <tr>
                            <td>Ejido</td>
                            <td>{{ $record->ejido_inmueble }}</td>
                        </tr>
                        <tr>
                            <td>Poblado</td>
                            <td>{{ $record->poblado_inmueble }}</td>
                        </tr>
                        <tr>
                            <td>Municipio</td>
                            <td>{{ $record->municipio_inmueble }}</td>
                        </tr>
                        <tr>
                            <td>Estado</td>
                            <td>{{ $record->estado_inmueble }}</td>
                        </tr>
                        <tr>
                            <td>Regimen de propiedad</td>
                            <td>{{ $record->regimen_propiedad_inmueble }}</td>
                        </tr>
                        <tr>
                            <td>Uso de suelo</td>
                            <td>{{ $record->uso_suelo_inmueble }}</td>
                        </tr>
                    </tbody>
                </table>
                <br><br><br><br>
                <h4 style="margin-top: 70px">Superficies a contratar</h4>

                <table class="mt-2 table table-bordered">
                    <tbody>
                        <tr>
                            <td>Tipo de afectación</td>
                            <td>{{ $record->tipo_afectacion_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Superficie contratado m2</td>
                            <td>{{ $record->superficie_contratada_m2_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Superficie m2 franja uso temporal</td>
                            <td>{{ $record->superficia_m2_franja_uso_temporal_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Superficie m2 fute</td>
                            <td>{{ $record->superficie_m2_fute_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Superficie total contratado m2</td>
                            <td>{{ $record->superficie_total_contratada_m2_superficie }}</td>
                        </tr>
                        <tr>
                            <td>km inicial</td>
                            <td>{{ $record->km_inicial_superficie }}</td>
                        </tr>
                        <tr>
                            <td>km final</td>
                            <td>{{ $record->km_final_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Longitud de afectacion</td>
                            <td>{{ $record->longitud_afectacion_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Coordenada e</td>
                            <td>{{ $record->coordenada_e_superficie }}</td>
                        </tr>
                        <tr>
                            <td>Coordenada n</td>
                            <td>{{ $record->coordenada_n_superficie }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($key != count($records) - 1)
                <div class="page-break"></div>
            @endif
        @endforeach
        <!--
    <div class="page-break"></div>
    -->
    </main>
</body>

</html>

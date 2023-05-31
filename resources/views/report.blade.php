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
        td {
            font-size: 16px;
            padding: 4px !important;
        }
    </style>
</head>


<body>
    <main>
        @foreach ($records as $key => $record)
            <div class="container">
                <table class="table table-bordered" style="border: 0px solid white">
                    <tr style="border: 0px solid white">
                        <td style="border: 0px solid white">
                            <p>
                                <img class src="https://files.inovuz.com/files/gasera/mexico.jpeg" style="width: 90px">
                            </p>
                        </td>
                        <td style="border: 0px solid white">
                            <br>
                            <p class="text-center text-bold" style="font-size: 18px; font-weight: bold">Sierra Madre</p>
                        </td>
                        <td style="border: 0px solid white">
                            <p class="text-right">
                                <img src="https://files.inovuz.com/files/gasera/blapp.jpeg" style="width: 90px;">
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 18px; font-weight: bold" class="text-center text-bold">Planilla de Identificación</p>
                <br>

                <table class="table table-bordered">
                    <tr>
                        <td style="width: 250px">Número de expediente:</td>
                        <td>
                            <strong>{{ $record->numero_expediente }}</strong>
                        </td>
                    </tr>
                </table>

                <table class="mt-2 table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 250px">Nombre de propietario/dependencia:</td>
                            <td>{{ $record->nombre_propietario_dependencia }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Celular, teléfono/recados:</td>
                            <td>{{ $record->telefono_recados }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Correo electrónico:</td>
                            <td>{{ $record->correo_electronico }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Calificación del propietario:</td>
                            <td>{{ $record->calificacion_propietario }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Dirección del propietario:</td>
                            <td>{{ $record->direccion_propietario_notificaciones }}</td>
                        </tr>
                        @if ($record->representante_legal)
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
                        @endif
                        <tr>
                            <td style="width: 250px">Observaciones:</td>
                            <td>{{ $record->observaciones_representante_legal }}</td>
                        </tr>
                    </tbody>
                </table>
                <br>

                <p style="margin-top: 10px; font-size: 18px; font-weight: bold" class="text-bold">Datos del inmueble a contratar</p>

                <table class="mt-2 table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 250px">Dirección del inmueble:</td>
                            <td>{{ $record->direccion_inmueble }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Ejido:</td>
                            <td>{{ $record->ejido_inmueble }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Poblado:</td>
                            <td>{{ $record->poblado_inmueble }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Municipio:</td>
                            <td>{{ $record->municipio_inmueble }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Estado:</td>
                            <td>{{ $record->estado_inmueble }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Régimen de propiedad:</td>
                            <td>{{ $record->regimen_propiedad_inmueble }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Uso de suelo:</td>
                            <td>{{ $record->uso_suelo_inmueble }}</td>
                        </tr>
                    </tbody>
                </table>
                <br><br><br><br>
                <!--
                <h4 style="margin-top: 70px">Superficies a contratar</h4>

                <table class="mt-2 table table-bordered">
                    <tbody>
                        <tr>
                            <td style="width: 250px">Tipo de afectación:</td>
                            <td>{{ $record->tipo_afectacion_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Superficie contratado m2:</td>
                            <td>{{ $record->superficie_contratada_m2_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Superficie m2 franja uso temporal:</td>
                            <td>{{ $record->superficia_m2_franja_uso_temporal_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Superficie m2 fute:</td>
                            <td>{{ $record->superficie_m2_fute_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Superficie total contratado m2:</td>
                            <td>{{ $record->superficie_total_contratada_m2_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">km inicial:</td>
                            <td>{{ $record->km_inicial_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">km final:</td>
                            <td>{{ $record->km_final_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Longitud de afectacion:</td>
                            <td>{{ $record->longitud_afectacion_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Coordenada e:</td>
                            <td>{{ $record->coordenada_e_superficie }}</td>
                        </tr>
                        <tr>
                            <td style="width: 250px">Coordenada n:</td>
                            <td>{{ $record->coordenada_n_superficie }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            -->
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

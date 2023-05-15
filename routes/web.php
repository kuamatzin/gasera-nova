<?php

use Illuminate\Support\Facades\Route;
use App\Imports\RecordsImport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/records-import', function () {
    Excel::import(new RecordsImport, 'sonora.csv');

    return 'great';
});

Route::get('/records-sonora-import', function () {
    Excel::import(new \App\Imports\RecordsSonoraImport, 'sonora.csv');

    return 'great';
});

Route::get('/users-import', function () {
    Excel::import(new UsersImport, 'users.csv');

    return 'great';
});

function updatePropiedad(): void
{
    $records = \App\Models\Record::all();

    foreach ($records as $record) {
        $keys = ['pr', 'pa', 'ej'];

        if (in_array($record->regimen_propiedad_inmueble, $keys)) {
            continue;
        }

        $propiedades = [
            'PROPIEDAD PRIVADA' => 'pr',
            'PARCELA' => 'pa',
            'EJIDO' => 'ej',
        ];

        $record->update([
            'regimen_propiedad_inmueble' => $propiedades[$record->regimen_propiedad_inmueble]
        ]);
    }
}

function updateState(): void
{
    $records = \App\Models\Record::all();

    foreach ($records as $record) {
        $record->update(
            ['estado_inmueble' => strtolower($record->estado_inmueble)]
        );
    }
}

function updateMunicipio(): void
{
    $records = \App\Models\Record::where('estado_inmueble', 'sonora')->get();

    $chihuahua = [
        'villaahumada' => '001',
        'buenaventura' => '010',
        'casasgrandes' => '013',
        'galeana' => '023',
        'guadalupe' => '028',
        'nuevocasasgrandes' => '050',
    ];

    $sonora = [
        'bacerac' => '010',
        'cumpas' => '023',
        'huachinera' => '031',
        'pitiquito' => '047',
        'trincheras' => '064',
        'cucurpe' => '022',
        'santaana' => '058',
        'arizpe' => '006',
        'villahidalgo' => '067',
    ];

    $keys = array_values($sonora);

    foreach ($records as $record) {
        if (!in_array($record->municipio_inmueble, $keys)) {
            $record->update(
                ['municipio_inmueble' => $sonora[str_replace(' ', '', strtolower($record->municipio_inmueble))]]
            );
        }
    }
}

function updateNumeroCadenamiento(): void
{
    $records = \App\Models\Record::orderBy('created_at', 'asc')->get();

    foreach ($records as $key => $record) {
        $record->update(
            ['numero_cadenamiento' => $key + 1]
        );
    }
}

Route::get('/test', function () {
    dd(\App\Models\Record::where('user_id', 1)->get());
});

Route::get('files', function () {
    $docsFaseUno = ['atp', 'aus', 'clg', 'paf', 'cbdts', 'fbdts', 'cpf'];
    $docsFaseDos = ['ain', 'sener', 'sedatu', 'adc', 'cto', 'cre', 'csedatu', 'icr'];

    array_filter(\Illuminate\Support\Facades\Storage::disk('public')->files(), function ($item) use ($docsFaseUno, $docsFaseDos) {
        if (str_contains($item, '.pdf') || str_contains($item, '.PDF')) {
            $numero_expediente_pos = strpos($item, '_');
            $numero_expediente = substr($item, 0, $numero_expediente_pos);
            $archivo = substr($item, $numero_expediente_pos + 1, strlen($item));
            $archivo = strtolower(str_replace('.pdf', '', $archivo));
            $expediente = \App\Models\Record::where('numero_expediente', $numero_expediente)->first();
            if ($expediente) {
                if (in_array($archivo, $docsFaseUno)) {
                    $dictamen_legal_fase_uno = $expediente->dictamen_legal_fase_uno;
                    $key_file = $archivo;
                    $dictamen_legal_fase_uno[$key_file] = $item;
                    $expediente->update([
                        'dictamen_legal_fase_uno' => $dictamen_legal_fase_uno
                    ]);
                } elseif (in_array($archivo, $docsFaseDos)) {
                    $dictamen_legal_fase_dos = $expediente->dictamen_legal_fase_dos;
                    $key_file = $archivo;
                    $dictamen_legal_fase_dos[$key_file] = $item;
                    $expediente->update([
                        'dictamen_legal_fase_dos' => $dictamen_legal_fase_dos
                    ]);
                } else {
                    $documentacion = $expediente->documentacion;
                    $key_file = $archivo . '_' . $expediente->regimen_propiedad_inmueble;
                    $documentacion[$key_file] = $item;
                    $expediente->update([
                        'documentacion' => $documentacion
                    ]);
                }
            }
        }
    });
});

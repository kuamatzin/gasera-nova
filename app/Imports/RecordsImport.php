<?php

namespace App\Imports;

use App\Models\Record;
use Maatwebsite\Excel\Concerns\ToModel;

class RecordsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($row[36]) {
            $codeMunicipio = (string)$row[3];
            if (strlen($codeMunicipio) == 1) {
                $codeMunicipio = '00' . $codeMunicipio;
            } elseif (strlen($codeMunicipio) == 2) {
                $codeMunicipio = '0' . $codeMunicipio;
            }

            $codeNumero = (string)$row[5];
            if (strlen($codeNumero) == 1) {
                $codeNumero = '00' . $codeNumero;
            } elseif (strlen($codeNumero) == 2) {
                $codeNumero = '0' . $codeNumero;
            }

            $numero_expediente = str_replace(' ', '', $row[1]) . '-' . str_replace(' ', '', $row[2]) . '-' . $codeMunicipio . '-' . str_replace(' ', '', $row[4]) . '-' . $codeNumero;

            return new Record([
                'user_id' => $row[36],
                'numero_expediente' => $numero_expediente,
                'nombre_propietario_dependencia' => $row[7],
                'municipio_inmueble' => $row[22],
                'estado_inmueble' => $row[23],
                'regimen_propiedad_inmueble' => $row[24],
                'km_inicial_superficie' => $row[32],
                'km_final_superficie' => $row[33],
                'longitud_afectacion_superficie' => $row[34],
                'status' => 'progress',
            ]);
        }
    }
}

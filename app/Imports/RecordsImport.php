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
        return new Record([
            'user_id' => 1,
            'nombre_propietario_dependencia' => $row[7],
            'municipio_inmueble' => $row[22],
            'estado_inmueble' => $row[23],
            'regimen_propiedad_inmueble' => $row[24],
            'km_inicial_superficie' => $row[32],
            'km_final_superficie' => $row[33],
            'longitud_afectacion_superficie' => $row[34],
        ]);
    }
}

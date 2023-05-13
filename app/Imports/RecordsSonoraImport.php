<?php

namespace App\Imports;

use App\Models\Record;
use Maatwebsite\Excel\Concerns\ToModel;

class RecordsSonoraImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Record([
            'user_id' => $row[36],
            'numero_expediente' => $row[1],
            'nombre_propietario_dependencia' => $row[11],
            'municipio_inmueble' => $row[9],
            'estado_inmueble' => $row[8],
            'regimen_propiedad_inmueble' => $row[10],
            'km_inicial_superficie' => $row[19],
            'km_final_superficie' => $row[20],
            'status' => 'progress',
        ]);
    }
}

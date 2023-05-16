<?php

namespace App\Imports;

use App\Models\Record;
use Maatwebsite\Excel\Concerns\ToModel;

class RecodsNumberFormat implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $record = Record::where('numero_expediente', $row[1])->first();
        if ($record) {
            $record->update([
                'numero_cadenamiento' => $row[0],
            ]);
        }
    }
}

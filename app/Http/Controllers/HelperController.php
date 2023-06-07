<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function uploadKmz(Request $request, $id)
    {
        $file = $request->file('file');
        $path = $request->file('file')->storeAs('avatars', time() . '.' . $file->getClientOriginalExtension(), 'public');

        $record = Record::findOrFail($id);

        $record->mapa_afectacion_path = $path;
        $record->save();

        return $record;
    }

    public function getLatitudeLongitude(Request $request, $id)
    {
        return [
            'sonora' => Record::findOrFail(2),
            'chihuahua' => Record::findOrFail(1),
            'detail' => Record::findOrFail($id)
        ];
    }

    public function getKmz(Request $request, $id)
    {
        $record = Record::findOrFail($id);

        return $record;
    }
}

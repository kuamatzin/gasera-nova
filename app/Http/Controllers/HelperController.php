<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function uploadKmz(Request $request)
    {
        $file = $request->file('file');
        $path = $request->file('file')->storeAs('avatars', time() . $file->getClientOriginalExtension(), 'public');

        $record = Record::findOrFail(1);

        $record->mapa_afectacion_path = $path;
        $record->save();

        return $record;
    }
}

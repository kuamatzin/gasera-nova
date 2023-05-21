<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        $records = Record::where('numero_cadenamiento', '<', 20)->get();

        view()->share('records', $records);
        view()->share('download', true);
        $pdf = Pdf::loadView('report');

        return $pdf->download('report.pdf');
    }
}

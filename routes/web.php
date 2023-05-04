<?php

use Illuminate\Support\Facades\Route;
use App\Imports\RecordsImport;
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
   Excel::import(new RecordsImport, 'bd.csv');

   return 'great';
});

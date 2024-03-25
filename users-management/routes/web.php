<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Services\ExportRequests;
use App\Http\Middleware\CheckRoute;


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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('requests/export', ExportRequests::class)->name('requests.export');
Route::middleware(CheckRoute::class)->get('requests/export', ExportRequests::class)->name('requests.export');


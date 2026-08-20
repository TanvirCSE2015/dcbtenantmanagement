<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::redirect('/', '/admin/login');

Route::get('/print-single-tenant',[PrintController::class,'PrintSingleTenant'])
    ->name('single-tenant.print')->middleware('auth');

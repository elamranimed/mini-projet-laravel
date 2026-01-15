<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoapServerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/books-app.html', function () {
    return file_get_contents(public_path('books-app.html'));
});

// SOAP Routes
Route::post('/soap', [SoapServerController::class, 'handle']);
Route::get('/soap/wsdl', [SoapServerController::class, 'wsdl']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return view('registro');
});

Route::post('/registro', [UsuarioController::class, 'registrar']);
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\UsuarioController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Página principal / Landing page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Registro de usuarios
|--------------------------------------------------------------------------
| GET  -> muestra el formulario de registro
| POST -> procesa el registro
|--------------------------------------------------------------------------
*/

Route::get('/registro', function () {
    return view('registro');
});

Route::post('/registro', [UsuarioController::class, 'registrar']);


/*
|--------------------------------------------------------------------------
| Login y logout
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', [UsuarioController::class, 'login']);

Route::get('/logout', [UsuarioController::class, 'logout']);


/*
|--------------------------------------------------------------------------
| Dashboard paciente
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
});


/*
|--------------------------------------------------------------------------
| Vistas secundarias del paciente
|--------------------------------------------------------------------------
*/

Route::get('/citas', function () {
    return view('citas');
});

Route::get('/diario', function () {
    return view('diario');
});

Route::get('/ayuda', function () {
    return view('ayuda');
});


/*
|--------------------------------------------------------------------------
| Panel terapeuta
|--------------------------------------------------------------------------
*/

Route::get('/terapeuta', function () {
    return view('terapeuta');
});

Route::get('/confirmar', function () {
    return view('confirmar');
});


/*
|--------------------------------------------------------------------------
| Pacientes vinculados al terapeuta
|--------------------------------------------------------------------------
*/

Route::get('/pacientes', function () {

    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $pacientes = DB::table('users')
        ->where(function ($query) use ($terapeutaId) {
            $query->where('terapeuta_id', (int) $terapeutaId)
                  ->orWhere('terapeuta_id', (string) $terapeutaId);
        })
        ->where(function ($query) {
            $query->where('terapeuta', 0)
                  ->orWhere('terapeuta', '0');
        })
        ->get();

    return view('pacientes', compact('pacientes'));

});


/*
|--------------------------------------------------------------------------
| Expediente dinámico del paciente
|--------------------------------------------------------------------------
*/

Route::get('/expediente/{id}', function ($id) {

    $paciente = User::find($id);

    if (!$paciente) {
        abort(404);
    }

    return view('expediente', compact('paciente'));

});


/*
|--------------------------------------------------------------------------
| Generar PIN de vinculación para terapeuta
|--------------------------------------------------------------------------
*/

Route::post('/generar-pin', function () {

    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    $terapeuta = DB::table('users')
        ->where('id', $usuarioId)
        ->first();

    if (!$terapeuta) {
        return redirect('/login');
    }

    if (
        $terapeuta->codigo_vinculacion &&
        $terapeuta->codigo_expira_en &&
        now()->lt($terapeuta->codigo_expira_en)
    ) {
        return redirect('/terapeuta')
            ->with('pin_existente', 'Ya tienes un PIN activo.');
    }

    $nuevoPin = rand(100000, 999999);

    DB::table('users')
        ->where('id', $usuarioId)
        ->update([
            'codigo_vinculacion' => $nuevoPin,
            'codigo_expira_en' => now()->addDays(90),
            'updated_at' => now(),
        ]);

    return redirect('/terapeuta')
        ->with('success_pin', 'PIN generado correctamente.');

});


/*
|--------------------------------------------------------------------------
| Vincular paciente con terapeuta mediante PIN
|--------------------------------------------------------------------------
*/

Route::post('/vincular-terapeuta', function (Request $request) {

    $request->validate([
        'codigo' => 'required|string|max:6',
        'motivo' => 'required|string',
    ]);

    $codigo = trim($request->codigo);

    $terapeuta = DB::table('users')
        ->where('codigo_vinculacion', $codigo)
        ->where('terapeuta', 1)
        ->first();

    if (!$terapeuta) {
        return redirect('/dashboard')
            ->with('error_vinculacion', 'PIN inválido.');
    }

    if (
        $terapeuta->codigo_expira_en &&
        now()->gt($terapeuta->codigo_expira_en)
    ) {
        return redirect('/dashboard')
            ->with('error_vinculacion', 'El PIN ha expirado. Solicita uno nuevo a tu terapeuta.');
    }

    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    DB::table('users')
        ->where('id', $usuarioId)
        ->update([
            'terapeuta_id' => (int) $terapeuta->id,
            'motivo_terapia' => Crypt::encryptString($request->motivo),
            'updated_at' => now(),
        ]);

    session([
        'terapeuta_vinculado' => true,
        'nombre_terapeuta' => $terapeuta->nombre . ' ' . $terapeuta->apellido,
    ]);

    return redirect('/dashboard')
        ->with('success_vinculacion', 'Terapeuta vinculado exitosamente.');

});
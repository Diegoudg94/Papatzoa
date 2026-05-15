<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Página principal
|--------------------------------------------------------------------------
|
| Muestra el formulario de registro
|
*/

Route::get('/', function () {
    return view('registro');
});


/*
|--------------------------------------------------------------------------
| Registro de usuarios
|--------------------------------------------------------------------------
|
| Procesa el formulario de registro
|
*/

Route::post('/registro', [UsuarioController::class, 'registrar']);


/*
|--------------------------------------------------------------------------
| Vista login
|--------------------------------------------------------------------------
|
| Muestra la pantalla de login
|
*/

Route::get('/login', function () {
    return view('login');
});
Route::get('/logout', [UsuarioController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Procesar login
|--------------------------------------------------------------------------
|
| Temporalmente solo redirige al dashboard
| Más adelante aquí irá autenticación real
|
*/
Route::post('/login', [UsuarioController::class, 'login']);
/*
|--------------------------------------------------------------------------
| Dashboard principal
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
});


/*
|--------------------------------------------------------------------------
| Vistas secundarias
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

Route::get('/pacientes', function () {

    // Obtener ID terapeuta desde sesión
    $terapeutaId = session('usuario_id');

    // Evitar que falle o busque "null" si la sesión expiró
    if (!$terapeutaId) {
        return redirect('/login');
    }

    // Buscar pacientes vinculados
    $pacientes = DB::table('users')
        ->where(function ($query) use ($terapeutaId) {
            // Buscamos tanto en formato numérico como en texto 
            // (por si SQLite lo guardó como String anteriormente)
            $query->where('terapeuta_id', (int) $terapeutaId)
                  ->orWhere('terapeuta_id', (string) $terapeutaId);
        })
        ->where(function ($query) {
            $query->where('terapeuta', 0)
                  ->orWhere('terapeuta', '0');
        })
        ->get();

    return view(
        'pacientes',
        compact('pacientes')
    );

});

Route::get('/terapeuta', function () {
    return view('terapeuta');
});

Route::get('/confirmar', function () {
    return view('confirmar');
});

Route::get('/expediente/{id}', function ($id) {

    $paciente = User::find($id);

    return view('expediente', compact('paciente'));

});

Route::post('/generar-pin', function () {

    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    // Buscar terapeuta
    $terapeuta = DB::table('users')
        ->where('id', $usuarioId)
        ->first();

    if (!$terapeuta) {
        return redirect('/login');
    }

    // Si ya tiene PIN vigente
    if (

        $terapeuta->codigo_vinculacion &&
        $terapeuta->codigo_expira_en &&
        now()->lt($terapeuta->codigo_expira_en)

    ) {

        return redirect('/terapeuta')
            ->with(
                'pin_existente',
                'Ya tienes un PIN activo.'
            );

    }

    // Generar nuevo PIN
    $nuevoPin = rand(100000, 999999);

    DB::table('users')
        ->where('id', $usuarioId)
        ->update([

            'codigo_vinculacion' => $nuevoPin,

            'codigo_expira_en' =>
                now()->addDays(90)

        ]);

    return redirect('/terapeuta')
        ->with(
            'success_pin',
            'PIN generado correctamente.'
        );

});

Route::post('/vincular-terapeuta', function (Request $request) {

    $codigo = trim($request->codigo);

    // Buscar terapeuta
    $terapeuta = DB::table('users')
        ->where('codigo_vinculacion', $codigo)
        ->where('terapeuta', 1)
        ->first();

    // Validar PIN
    if (!$terapeuta) {

        return redirect('/dashboard')
            ->with(
                'error_vinculacion',
                'PIN inválido.'
            );

    }

    // Obtener usuario actual
    $usuarioId = session('usuario_id');

    // Guardar vínculo
    DB::table('users')
        ->where('id', $usuarioId)
        ->update([

            'terapeuta_id' => (int) $terapeuta->id,

            'motivo_terapia' =>
    Crypt::encryptString($request->motivo)

        ]);

    // Guardar en sesión
    session([

        'terapeuta_vinculado' => true,

        'nombre_terapeuta' =>
            $terapeuta->nombre . ' ' . $terapeuta->apellido

    ]);

    return redirect('/dashboard')
        ->with(
            'success_vinculacion',
            'Terapeuta vinculado exitosamente.'
        );

});

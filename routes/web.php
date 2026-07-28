<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TherapistAvailabilityController;
use App\Http\Controllers\PatientAppointmentController;
use App\Models\Cita;
use App\Models\DiarioEmocion;
use App\Models\NotaTerapeuta;
use App\Models\SeguimientoEmocion;
use App\Models\TherapistCredential;
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
*/

Route::get('/registro', function () {
    return view('registro');
});

Route::post('/registro', [UsuarioController::class, 'registrar']);

Route::get('/registro/google', function () {
    $supabaseUrl = rtrim(config('services.supabase.url'), '/');
    $supabaseUrl = Str::replaceEnd('/rest/v1', '', $supabaseUrl);

    $redirectTo = url('/registro/google/callback');

    return redirect()->away(
        $supabaseUrl . '/auth/v1/authorize?' . http_build_query([
            'provider' => 'google',
            'redirect_to' => $redirectTo,
        ])
    );
});

Route::get('/registro/google/callback', function () {
    $supabaseUrl = rtrim(config('services.supabase.url'), '/');
    $supabaseUrl = Str::replaceEnd('/rest/v1', '', $supabaseUrl);

    return view('registro-google-callback', [
        'supabaseUrl' => $supabaseUrl,
        'supabaseAnonKey' => config('services.supabase.anon_key'),
    ]);
});

Route::post('/registro/google/callback', function (Request $request) {
    $request->validate([
        'supabase_id' => 'required|string|max:255',
        'nombre' => 'required|string|max:255',
        'correo' => 'required|email|max:255',
        'avatar_url' => 'nullable|url|max:2048',
    ]);

    $usuario = DB::table('users')
        ->where('correo', $request->correo)
        ->first();

    if ($usuario) {
        DB::table('users')
            ->where('id', $usuario->id)
            ->update([
                'supabase_id' => $usuario->supabase_id ?: $request->supabase_id,
                'avatar_url' => $request->avatar_url,
                'auth_provider' => 'google',
                'updated_at' => now(),
            ]);

        $request->session()->regenerate();

        session([
            'usuario_id' => (int) $usuario->id,
            'usuario_nombre' => $usuario->nombre,
            'usuario_apellido' => $usuario->apellido,
            'usuario_correo' => $usuario->correo,
            'usuario_terapeuta' => (int) $usuario->terapeuta,
        ]);

        return response()->json([
            'redirect' => $usuario->terapeuta ? url('/terapeuta') : url('/dashboard'),
        ]);
    }

    session([
        'google_registro' => [
            'supabase_id' => $request->supabase_id,
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'avatar_url' => $request->avatar_url,
        ],
    ]);

    return response()->json([
        'redirect' => url('/completar-registro-google'),
    ]);
});

Route::get('/completar-registro-google', function () {
    $googleUser = session('google_registro');

    if (!$googleUser) {
        return redirect('/registro');
    }

    return view('completar-registro-google', compact('googleUser'));
});

Route::post('/completar-registro-google', function (Request $request) {
    $googleUser = session('google_registro');

    if (!$googleUser) {
        return redirect('/registro');
    }

    $request->validate([
        'nombre' => 'required|string|max:255',
        'edad' => 'required|integer|min:10|max:120',
        'sexo' => 'required|string|in:masculino,femenino,no-binario,otro,prefiero_no_decir',
        'terapeuta' => 'required|boolean',
    ]);

    $usuario = DB::table('users')
        ->where('correo', $googleUser['correo'])
        ->first();

    if ($usuario) {
        session()->forget('google_registro');

        $request->session()->regenerate();

        session([
            'usuario_id' => (int) $usuario->id,
            'usuario_nombre' => $usuario->nombre,
            'usuario_apellido' => $usuario->apellido,
            'usuario_correo' => $usuario->correo,
            'usuario_terapeuta' => (int) $usuario->terapeuta,
        ]);

        return redirect($usuario->terapeuta ? '/terapeuta' : '/dashboard');
    }

    $esTerapeuta = (bool) $request->boolean('terapeuta');

    $usuarioId = DB::table('users')->insertGetId([
        'supabase_id' => $googleUser['supabase_id'],
        'nombre' => $request->nombre,
        'apellido' => null,
        'sexo' => $request->sexo,
        'edad' => $request->edad,
        'correo' => $googleUser['correo'],
        'password' => null,
        'avatar_url' => $googleUser['avatar_url'] ?? null,
        'auth_provider' => 'google',
        'terapeuta' => $esTerapeuta ? 1 : 0,
        'terapeuta_verificado' => false,
        'estado_verificacion' => $esTerapeuta ? 'pendiente' : 'no_aplica',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    session()->forget('google_registro');

    $request->session()->regenerate();

    session([
        'usuario_id' => (int) $usuarioId,
        'usuario_nombre' => $request->nombre,
        'usuario_apellido' => null,
        'usuario_correo' => $googleUser['correo'],
        'usuario_terapeuta' => $esTerapeuta ? 1 : 0,
    ]);

    if ($esTerapeuta) {
        return redirect('/terapeuta')
            ->with('verificacion_pendiente', 'Tu cuenta de terapeuta quedó pendiente de verificación profesional.');
    }

    return redirect('/dashboard');
});


/*
|--------------------------------------------------------------------------
| Login y logout
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    $supabaseUrl = rtrim(config('services.supabase.url'), '/');
    $supabaseUrl = Str::replaceEnd('/rest/v1', '', $supabaseUrl);

    return view('login', [
        'supabaseUrl' => $supabaseUrl,
        'supabaseAnonKey' => config('services.supabase.anon_key'),
        'googleLoginRedirectTo' => url('/login/google/callback'),
    ]);
});

Route::post('/login', [UsuarioController::class, 'login']);

Route::get('/login/google/callback', function () {
    $supabaseUrl = rtrim(config('services.supabase.url'), '/');
    $supabaseUrl = Str::replaceEnd('/rest/v1', '', $supabaseUrl);

    return view('login-google-callback', [
        'supabaseUrl' => $supabaseUrl,
        'supabaseAnonKey' => config('services.supabase.anon_key'),
    ]);
});

Route::post('/login/google/validar', function (Request $request) {
    $request->validate([
        'email' => 'required|email|max:255',
        'supabase_id' => 'required|string|max:255',
        'name' => 'nullable|string|max:255',
        'avatar_url' => 'nullable|url|max:2048',
    ]);

    $usuario = DB::table('users')
        ->where('correo', $request->email)
        ->orWhere('supabase_id', $request->supabase_id)
        ->first();

    if (!$usuario) {
        session()->flash(
            'google_login_error',
            'No encontramos una cuenta asociada a este correo. Regístrate primero con Google.'
        );

        return response()->json([
            'redirect' => url('/registro'),
        ], 404);
    }

    DB::table('users')
        ->where('id', $usuario->id)
        ->update([
            'supabase_id' => $usuario->supabase_id ?: $request->supabase_id,
            'avatar_url' => $request->avatar_url ?: $usuario->avatar_url,
            'auth_provider' => $usuario->auth_provider ?: 'google',
            'updated_at' => now(),
        ]);

    $request->session()->regenerate();

    session([
        'usuario_id' => (int) $usuario->id,
        'usuario_nombre' => $usuario->nombre,
        'usuario_apellido' => $usuario->apellido,
        'usuario_correo' => $usuario->correo,
        'usuario_terapeuta' => (int) $usuario->terapeuta,
    ]);

    return response()->json([
        'redirect' => $usuario->terapeuta ? url('/terapeuta') : url('/dashboard'),
    ]);
});

Route::get('/logout', [UsuarioController::class, 'logout']);

Route::get('/terapeuta/disponibilidad', [TherapistAvailabilityController::class, 'index'])
    ->name('therapist.availability.index');
Route::post('/terapeuta/disponibilidad/configuracion', [TherapistAvailabilityController::class, 'updateSettings'])
    ->name('therapist.availability.settings.update');
Route::post('/terapeuta/disponibilidad/horarios', [TherapistAvailabilityController::class, 'storeRule'])
    ->name('therapist.availability.rules.store');
Route::put('/terapeuta/disponibilidad/horarios/{id}', [TherapistAvailabilityController::class, 'updateRule'])
    ->whereNumber('id')
    ->name('therapist.availability.rules.update');
Route::delete('/terapeuta/disponibilidad/horarios/{id}', [TherapistAvailabilityController::class, 'destroyRule'])
    ->whereNumber('id')
    ->name('therapist.availability.rules.destroy');
Route::post('/terapeuta/disponibilidad/excepciones', [TherapistAvailabilityController::class, 'storeException'])
    ->name('therapist.availability.exceptions.store');
Route::delete('/terapeuta/disponibilidad/excepciones/{id}', [TherapistAvailabilityController::class, 'destroyException'])
    ->whereNumber('id')
    ->name('therapist.availability.exceptions.destroy');
Route::get('/terapeuta/disponibilidad/vista-previa', [TherapistAvailabilityController::class, 'preview'])
    ->name('therapist.availability.preview');
Route::get('/citas/disponibilidad', [PatientAppointmentController::class, 'availability'])
    ->name('patient.appointments.availability');


/*
|--------------------------------------------------------------------------
| Dashboard paciente
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    $usuario = DB::table('users')
        ->where('id', $usuarioId)
        ->first();

    if (!$usuario) {
        session()->flush();
        return redirect('/login');
    }

    if ((int) $usuario->terapeuta === 1) {
        return redirect('/terapeuta');
    }

    $terapeuta = null;

    if ($usuario->terapeuta_id) {
        $terapeuta = DB::table('users')
            ->select([
                'id',
                'nombre',
                'apellido',
                'correo',
                'avatar_url',
                'profile_photo_path',
                'especialidad',
                'experiencia_anios',
                'modalidad_atencion',
                'nacionalidad',
                'telefono_lada',
                'telefono',
                'cedula_profesional',
                'institucion_formacion',
                'enfoque_terapeutico',
                'biografia',
                'estado_verificacion',
                'terapeuta_verificado',
                'pais_atencion',
                'estado_atencion',
                'ciudad_atencion',
                'direccion_atencion',
                'codigo_postal_atencion',
            ])
            ->where('id', $usuario->terapeuta_id)
            ->where('terapeuta', 1)
            ->first();
    }

    return view('dashboard', compact('usuario', 'terapeuta'));
});


/*
|--------------------------------------------------------------------------
| Citas del paciente
|--------------------------------------------------------------------------
*/

Route::get('/citas', [PatientAppointmentController::class, 'index']);


/*
|--------------------------------------------------------------------------
| Solicitar cita
|--------------------------------------------------------------------------
*/

Route::post('/citas/solicitar', [PatientAppointmentController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Vistas secundarias del paciente
|--------------------------------------------------------------------------
*/

Route::get('/diario', function () {

    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    $descifrar = function (?string $valor): ?string {
        if (!$valor) {
            return null;
        }

        try {
            return Crypt::decryptString($valor);
        } catch (\Throwable) {
            return 'No se pudo mostrar este dato.';
        }
    };

    $emociones = DiarioEmocion::with(['seguimientos' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->where('user_id', $usuarioId)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function (DiarioEmocion $emocion) use ($descifrar) {
            $emocion->situacion = $descifrar($emocion->situacion_encrypted);
            $emocion->pensamiento = $descifrar($emocion->pensamiento_encrypted);
            $emocion->conducta = $descifrar($emocion->conducta_encrypted);
            $emocion->interpretacion = $descifrar($emocion->interpretacion_encrypted);
            $emocion->reestructuracion = $descifrar($emocion->reestructuracion_encrypted);

            $emocion->seguimientos->each(function (SeguimientoEmocion $seguimiento) use ($descifrar) {
                $seguimiento->nota = $descifrar($seguimiento->nota_encrypted);
            });

            return $emocion;
        });

    return view('diario', compact('emociones'));
});

Route::post('/diario', function (Request $request) {

    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    $datos = $request->validate([
        'situacion' => 'nullable|string',
        'pensamiento' => 'nullable|string',
        'emocion' => 'required|string',
        'intensidad' => 'nullable|integer|min:1|max:10',
        'conducta' => 'nullable|string',
        'interpretacion' => 'nullable|string',
        'reestructuracion' => 'nullable|string',
    ]);

    $cifrar = fn (?string $valor): ?string => filled($valor)
        ? Crypt::encryptString($valor)
        : null;

    DiarioEmocion::create([
        'user_id' => $usuarioId,
        'emocion' => $datos['emocion'],
        'intensidad' => $datos['intensidad'] ?? null,
        'situacion_encrypted' => $cifrar($datos['situacion'] ?? null),
        'pensamiento_encrypted' => $cifrar($datos['pensamiento'] ?? null),
        'conducta_encrypted' => $cifrar($datos['conducta'] ?? null),
        'interpretacion_encrypted' => $cifrar($datos['interpretacion'] ?? null),
        'reestructuracion_encrypted' => $cifrar($datos['reestructuracion'] ?? null),
    ]);

    return redirect('/diario')
        ->with('success_diario', 'Tu emoción se guardó correctamente.');
});

Route::post('/diario/{id}/seguimiento', function (Request $request, $id) {

    $usuarioId = session('usuario_id');

    if (!$usuarioId) {
        return redirect('/login');
    }

    $emocion = DiarioEmocion::where('id', $id)
        ->where('user_id', $usuarioId)
        ->firstOrFail();

    $datos = $request->validate([
        'nota' => 'required|string|max:2000',
    ]);

    SeguimientoEmocion::create([
        'diario_emocion_id' => $emocion->id,
        'user_id' => $usuarioId,
        'nota_encrypted' => Crypt::encryptString($datos['nota']),
    ]);

    return redirect('/diario')
        ->with('success_diario', 'Seguimiento agregado correctamente.');
})->whereNumber('id');

Route::get('/ayuda', function () {
    return view('ayuda');
});


/*
|--------------------------------------------------------------------------
| Panel terapeuta
|--------------------------------------------------------------------------
*/

Route::get('/terapeuta/mis-datos', function () {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = User::where('id', $terapeutaId)->first();

    if (!$usuario || (int) $usuario->terapeuta !== 1) {
        abort(403);
    }

    $credenciales = TherapistCredential::where('terapeuta_id', $usuario->id)
        ->latest()
        ->get();

    return view('terapeuta-mis-datos', compact('usuario', 'credenciales'));
});

Route::post('/terapeuta/mis-datos', function (Request $request) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = User::where('id', $terapeutaId)->first();

    if (!$usuario || (int) $usuario->terapeuta !== 1) {
        abort(403);
    }

    if ($request->modalidad_atencion === 'online') {
        $request->merge([
            'pais_atencion' => null,
            'estado_atencion' => null,
            'ciudad_atencion' => null,
            'direccion_atencion' => null,
            'codigo_postal_atencion' => null,
        ]);
    }

    $requiereUbicacion = in_array($request->modalidad_atencion, ['presencial', 'hibrida'], true);
    $locationRequirement = $requiereUbicacion ? 'required' : 'nullable';

    $validated = $request->validate([
        'nacionalidad' => 'nullable|string|max:100',
        'telefono_lada' => 'nullable|string|max:10',
        'telefono' => 'nullable|string',
        'especialidad' => 'nullable|string|max:255',
        'biografia' => 'nullable|string|max:3000',
        'experiencia_anios' => 'nullable|integer|min:0|max:80',
        'cedula_profesional' => 'nullable|string|max:255',
        'institucion_formacion' => 'nullable|string|max:255',
        'enfoque_terapeutico' => 'nullable|string|max:255',
        'modalidad_atencion' => 'nullable|string|in:presencial,online,hibrida',
        'pais_atencion' => $locationRequirement . '|string|max:100',
        'estado_atencion' => $locationRequirement . '|string|max:100',
        'ciudad_atencion' => $locationRequirement . '|string|max:100',
        'direccion_atencion' => $locationRequirement . '|string|max:255',
        'codigo_postal_atencion' => $locationRequirement . '|string|max:20',
    ]);

    $telefonoLimpio = preg_replace('/\D/', '', $request->telefono ?? '');

    if ($telefonoLimpio !== '' && strlen($telefonoLimpio) !== 10) {
        return back()
            ->withErrors(['telefono' => 'El teléfono debe tener exactamente 10 dígitos.'])
            ->withInput();
    }

    $validated['telefono'] = $telefonoLimpio !== '' ? $telefonoLimpio : null;

    if (($validated['modalidad_atencion'] ?? null) === 'online') {
        $validated['pais_atencion'] = null;
        $validated['estado_atencion'] = null;
        $validated['ciudad_atencion'] = null;
        $validated['direccion_atencion'] = null;
        $validated['codigo_postal_atencion'] = null;
    }

    DB::table('users')
        ->where('id', $usuario->id)
        ->where('terapeuta', 1)
        ->update(array_merge($validated, [
            'updated_at' => now(),
        ]));

    return redirect('/terapeuta/mis-datos')
        ->with('success', 'Datos actualizados correctamente.');
});

Route::post('/terapeuta/mis-datos/foto', function (Request $request) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = User::where('id', $terapeutaId)->first();

    if (!$usuario || (int) $usuario->terapeuta !== 1) {
        abort(403);
    }

    $request->validate([
        'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $path = $request->file('foto')->store('profile_photos', 'public');

    DB::table('users')
        ->where('id', $usuario->id)
        ->where('terapeuta', 1)
        ->update([
            'profile_photo_path' => $path,
            'updated_at' => now(),
        ]);

    return redirect('/terapeuta/mis-datos')
        ->with('success', 'Foto actualizada correctamente.');
});

Route::post('/terapeuta/mis-datos/foto-google', function () {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$usuario) {
        abort(403);
    }

    DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->update([
            'profile_photo_path' => null,
            'updated_at' => now(),
        ]);

    return redirect('/terapeuta/mis-datos')
        ->with('success', 'Ahora estás usando tu foto de Google.');
});

Route::post('/terapeuta/mis-datos/credenciales', function (Request $request) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = User::where('id', $terapeutaId)->first();

    if (!$usuario || (int) $usuario->terapeuta !== 1) {
        abort(403);
    }

    $validated = $request->validate([
        'tipo_documento' => 'required|string|max:100',
        'documento' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
    ]);

    $path = $request->file('documento')->store('therapist_credentials');

    TherapistCredential::create([
        'terapeuta_id' => $usuario->id,
        'tipo_documento' => $validated['tipo_documento'],
        'archivo_path' => $path,
        'nombre_original' => $request->file('documento')->getClientOriginalName(),
        'estado' => 'pendiente',
    ]);

    DB::table('users')
        ->where('id', $usuario->id)
        ->where('terapeuta', 1)
        ->update([
            'estado_verificacion' => 'pendiente',
            'terapeuta_verificado' => false,
            'updated_at' => now(),
        ]);

    return redirect('/terapeuta/mis-datos')
        ->with('success', 'Documento enviado para revisión.');
});

Route::get('/terapeuta/mis-datos/credenciales/{id}/ver', function ($id) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$usuario) {
        abort(403);
    }

    $credencial = TherapistCredential::where('id', $id)
        ->where('terapeuta_id', $terapeutaId)
        ->first();

    if (!$credencial) {
        abort(404);
    }

    $disk = Storage::disk('public')->exists($credencial->archivo_path)
        ? 'public'
        : 'local';

    if (!Storage::disk($disk)->exists($credencial->archivo_path)) {
        abort(404);
    }

    return Storage::disk($disk)->response(
        $credencial->archivo_path,
        $credencial->nombre_original ?: null
    );
})->whereNumber('id');

Route::delete('/terapeuta/mis-datos/credenciales/{id}', function ($id) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $usuario = DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$usuario) {
        abort(403);
    }

    $credencial = TherapistCredential::where('id', $id)
        ->where('terapeuta_id', $terapeutaId)
        ->first();

    if (!$credencial) {
        abort(404);
    }

    if (Storage::disk('public')->exists($credencial->archivo_path)) {
        Storage::disk('public')->delete($credencial->archivo_path);
    }

    if (Storage::disk('local')->exists($credencial->archivo_path)) {
        Storage::disk('local')->delete($credencial->archivo_path);
    }

    $credencial->delete();

    return redirect('/terapeuta/mis-datos')
        ->with('success', 'Documento eliminado correctamente.');
})->whereNumber('id');

Route::get('/terapeuta', function () {

    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    // Citas aceptadas / próximas citas reales
    $proximasCitas = DB::table('citas')
        ->join('users', 'citas.paciente_id', '=', 'users.id')
        ->where(function ($query) use ($terapeutaId) {
            $query->where('citas.terapeuta_id', (int) $terapeutaId)
                  ->orWhere('citas.terapeuta_id', (string) $terapeutaId);
        })
        ->whereIn('citas.estado', ['aceptada', 'aceptado', 'confirmada', 'confirmado'])
        ->select(
            'citas.id',
            'citas.fecha',
            'citas.hora',
            'citas.starts_at',
            'citas.ends_at',
            'citas.timezone',
            'citas.modalidad',
            'citas.motivo_encrypted',
            'users.id as paciente_id',
            'users.nombre',
            'users.apellido'
        )
        ->orderByRaw('COALESCE(citas.starts_at, citas.fecha::timestamp) ASC')
        ->orderBy('citas.hora', 'asc')
        ->get();

    // Número real de citas pendientes
    $pendientesCount = DB::table('citas')
        ->where(function ($query) use ($terapeutaId) {
            $query->where('terapeuta_id', (int) $terapeutaId)
                  ->orWhere('terapeuta_id', (string) $terapeutaId);
        })
        ->where('estado', 'pendiente')
        ->count();

    return view('terapeuta', compact('proximasCitas', 'pendientesCount'));

});


/*
|--------------------------------------------------------------------------
| Confirmar citas del terapeuta
|--------------------------------------------------------------------------
*/

Route::get('/confirmar', function () {

    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $solicitudes = DB::table('citas')
        ->join('users', 'citas.paciente_id', '=', 'users.id')
        ->where(function ($query) use ($terapeutaId) {
            $query->where('citas.terapeuta_id', (int) $terapeutaId)
                  ->orWhere('citas.terapeuta_id', (string) $terapeutaId);
        })
        ->where('citas.estado', 'pendiente')
       ->select(
    'citas.id',
    'citas.fecha',
    'citas.hora',
    'citas.starts_at',
    'citas.ends_at',
    'citas.timezone',
    'citas.modalidad',
    'citas.motivo_encrypted',
    'citas.estado',
    'users.id as paciente_id',
    'users.nombre',
    'users.apellido'
)
        ->orderBy('citas.created_at', 'desc')
        ->get();

    return view('confirmar', compact('solicitudes'));

});


/*
|--------------------------------------------------------------------------
| Aceptar cita
|--------------------------------------------------------------------------
*/

Route::post('/citas/{id}/aceptar', function ($id) {

    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    try {
        DB::transaction(function () use ($id, $terapeutaId) {
            $cita = Cita::query()
                ->where('id', $id)
                ->where(function ($query) use ($terapeutaId) {
                    $query->where('terapeuta_id', (int) $terapeutaId)
                          ->orWhere('terapeuta_id', (string) $terapeutaId);
                })
                ->lockForUpdate()
                ->firstOrFail();

            if ($cita->starts_at && $cita->ends_at) {
                $conflict = Cita::query()
                    ->where('terapeuta_id', $cita->terapeuta_id)
                    ->whereKeyNot($cita->id)
                    ->whereIn('estado', ['pendiente', 'confirmada', 'confirmado', 'aceptada', 'aceptado'])
                    ->whereNotNull('starts_at')
                    ->whereNotNull('ends_at')
                    ->where('starts_at', '<', $cita->ends_at)
                    ->where('ends_at', '>', $cita->starts_at)
                    ->lockForUpdate()
                    ->exists();

                if ($conflict) {
                    throw new \RuntimeException('slot_ocupado');
                }
            }

            $cita->update([
                'estado' => 'confirmada',
                'confirmed_at' => now(),
            ]);
        });
    } catch (\RuntimeException $exception) {
        if ($exception->getMessage() === 'slot_ocupado') {
            return redirect('/confirmar')
                ->with('error_confirmar', 'Este horario ya fue ocupado por otra cita.');
        }

        throw $exception;
    }

    return redirect('/confirmar')
        ->with('success_confirmar', 'Cita aceptada correctamente.');

});


/*
|--------------------------------------------------------------------------
| Rechazar cita
|--------------------------------------------------------------------------
*/

Route::post('/citas/{id}/rechazar', function (Request $request, $id) {

    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $comentario = null;

    if ($request->comentario) {
        $comentario = Crypt::encryptString($request->comentario);
    }

    DB::table('citas')
        ->where('id', $id)
        ->where(function ($query) use ($terapeutaId) {
            $query->where('terapeuta_id', (int) $terapeutaId)
                  ->orWhere('terapeuta_id', (string) $terapeutaId);
        })
        ->update([
            'estado' => 'rechazada',
            'comentario_terapeuta' => $comentario,
            'cancelled_at' => null,
            'updated_at' => now(),
        ]);

    return redirect('/confirmar')
        ->with('success_confirmar', 'Cita rechazada correctamente.');

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
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $terapeuta = User::where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$terapeuta) {
        abort(403);
    }

    $paciente = User::where('id', $id)
        ->where('terapeuta', 0)
        ->firstOrFail();

    if ((int) $paciente->terapeuta_id !== (int) $terapeuta->id) {
        abort(403);
    }

    $descifrar = function (?string $valor): ?string {
        if (!$valor) {
            return null;
        }

        try {
            return Crypt::decryptString($valor);
        } catch (\Throwable) {
            return 'No se pudo descifrar este dato.';
        }
    };

    $emociones = DiarioEmocion::where('user_id', $paciente->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function (DiarioEmocion $emocion) use ($descifrar) {
            $emocion->situacion = $descifrar($emocion->situacion_encrypted);
            $emocion->pensamiento = $descifrar($emocion->pensamiento_encrypted);
            $emocion->conducta = $descifrar($emocion->conducta_encrypted);
            $emocion->interpretacion = $descifrar($emocion->interpretacion_encrypted);
            $emocion->reestructuracion = $descifrar($emocion->reestructuracion_encrypted);

            return $emocion;
        });

    $citas = Cita::where('paciente_id', $paciente->id)
        ->where(function ($query) use ($terapeuta) {
            $query->where('terapeuta_id', $terapeuta->id)
                ->orWhereNull('terapeuta_id');
        })
        ->orderBy('fecha', 'desc')
        ->orderBy('hora', 'desc')
        ->get()
        ->map(function (Cita $cita) use ($descifrar) {
            $cita->motivo = $descifrar($cita->motivo_encrypted);

            return $cita;
        });

    $notas = NotaTerapeuta::where('paciente_id', $paciente->id)
        ->where('terapeuta_id', $terapeuta->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function (NotaTerapeuta $nota) use ($descifrar) {
            $nota->nota = $descifrar($nota->nota_encrypted);

            return $nota;
        });

    $notasSesion = DB::table('notas_sesion')
        ->where('paciente_id', $paciente->id)
        ->where('terapeuta_id', $terapeuta->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($nota) {
            try {
                $nota->nota = Crypt::decryptString($nota->nota_encrypted);
            } catch (\Throwable) {
                $nota->nota = 'No se pudo descifrar esta nota.';
            }

            return $nota;
        });

    return view('expediente', compact(
        'paciente',
        'emociones',
        'citas',
        'notas',
        'notasSesion'
    ));
})->whereNumber('id');

Route::post('/expediente/{id}/notas', function (Request $request, $id) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        return redirect('/login');
    }

    $terapeuta = User::where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$terapeuta) {
        abort(403);
    }

    $paciente = User::where('id', $id)
        ->where('terapeuta', 0)
        ->firstOrFail();

    if ((int) $paciente->terapeuta_id !== (int) $terapeuta->id) {
        abort(403);
    }

    $datos = $request->validate([
        'nota' => 'required|string|max:3000',
    ]);

    NotaTerapeuta::create([
        'paciente_id' => $paciente->id,
        'terapeuta_id' => $terapeuta->id,
        'nota_encrypted' => Crypt::encryptString($datos['nota']),
    ]);

    return redirect('/expediente/' . $paciente->id . '?tab=notas')
        ->with('success_expediente', 'Nota guardada correctamente.');
})->whereNumber('id');


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

    return redirect('/dashboard')
        ->with('success_vinculacion', 'Terapeuta vinculado exitosamente.');

});

Route::post('/expediente/{pacienteId}/citas/{citaId}/nota', function (Request $request, $pacienteId, $citaId) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        abort(403);
    }

    $request->validate([
        'nota' => 'required|string|max:3000',
    ]);

    $terapeuta = DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$terapeuta) {
        abort(403);
    }

    $paciente = DB::table('users')
        ->where('id', $pacienteId)
        ->where('terapeuta', 0)
        ->where('terapeuta_id', $terapeuta->id)
        ->first();

    if (!$paciente) {
        abort(403);
    }

    $cita = DB::table('citas')
        ->where('id', $citaId)
        ->where('paciente_id', $pacienteId)
        ->first();

    if (!$cita) {
        abort(404);
    }

    DB::table('notas_sesion')->insert([
        'cita_id' => $citaId,
        'paciente_id' => $pacienteId,
        'terapeuta_id' => $terapeutaId,
        'nota_encrypted' => Crypt::encryptString($request->nota),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/expediente/' . $pacienteId . '?tab=citas')
        ->with('success_expediente', 'Nota de sesión guardada correctamente.');
})->whereNumber('pacienteId')->whereNumber('citaId');

Route::put('/expediente/{pacienteId}/citas/{citaId}/nota/{notaId}', function (Request $request, $pacienteId, $citaId, $notaId) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        abort(403);
    }

    $datos = $request->validate([
        'nota' => 'required|string|max:3000',
    ]);

    $terapeuta = DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$terapeuta) {
        abort(403);
    }

    $paciente = DB::table('users')
        ->where('id', $pacienteId)
        ->where('terapeuta', 0)
        ->where('terapeuta_id', $terapeuta->id)
        ->first();

    if (!$paciente) {
        abort(403);
    }

    $cita = DB::table('citas')
        ->where('id', $citaId)
        ->where('paciente_id', $paciente->id)
        ->first();

    if (!$cita) {
        abort(404);
    }

    $nota = DB::table('notas_sesion')
        ->where('id', $notaId)
        ->where('cita_id', $cita->id)
        ->where('paciente_id', $paciente->id)
        ->where('terapeuta_id', $terapeuta->id)
        ->first();

    if (!$nota) {
        abort(404);
    }

    DB::table('notas_sesion')
        ->where('id', $nota->id)
        ->update([
            'nota_encrypted' => Crypt::encryptString($datos['nota']),
            'updated_at' => now(),
        ]);

    return redirect('/expediente/' . $paciente->id . '?tab=citas')
        ->with('success_expediente', 'Nota actualizada correctamente.');
})->whereNumber('pacienteId')->whereNumber('citaId')->whereNumber('notaId');

Route::delete('/expediente/{pacienteId}/citas/{citaId}/nota/{notaId}', function ($pacienteId, $citaId, $notaId) {
    $terapeutaId = session('usuario_id');

    if (!$terapeutaId) {
        abort(403);
    }

    $terapeuta = DB::table('users')
        ->where('id', $terapeutaId)
        ->where('terapeuta', 1)
        ->first();

    if (!$terapeuta) {
        abort(403);
    }

    $paciente = DB::table('users')
        ->where('id', $pacienteId)
        ->where('terapeuta', 0)
        ->where('terapeuta_id', $terapeuta->id)
        ->first();

    if (!$paciente) {
        abort(403);
    }

    $cita = DB::table('citas')
        ->where('id', $citaId)
        ->where('paciente_id', $paciente->id)
        ->first();

    if (!$cita) {
        abort(404);
    }

    $nota = DB::table('notas_sesion')
        ->where('id', $notaId)
        ->where('cita_id', $cita->id)
        ->where('paciente_id', $paciente->id)
        ->where('terapeuta_id', $terapeuta->id)
        ->first();

    if (!$nota) {
        abort(404);
    }

    DB::table('notas_sesion')
        ->where('id', $nota->id)
        ->delete();

    return redirect('/expediente/' . $paciente->id . '?tab=citas')
        ->with('success_expediente', 'Nota eliminada correctamente.');
})->whereNumber('pacienteId')->whereNumber('citaId')->whereNumber('notaId');

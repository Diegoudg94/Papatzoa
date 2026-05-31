<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    public function registrar(Request $request)
    {
        // =========================
        // VALIDACIONES
        // =========================

        $request->validate([

            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',

'sexo' => 'required|string',

'edad' => 'required|integer|min:10|max:120',

            'correo' => 'required|email|unique:users,correo',

            'password' => [

                'required',

                'confirmed',

                'min:8',

                'regex:/[A-Z]/',

                'regex:/[0-9]/',

                'regex:/[@$!%*#?&_\-]/'

            ]

        ], [

            'password.confirmed' =>
                'Las contraseñas no coinciden.',

            'password.min' =>
                'La contraseña debe tener mínimo 8 caracteres.',

            'password.regex' =>
                'La contraseña debe incluir una mayúscula, un número y un símbolo especial.',

            'correo.unique' =>
                'Este correo ya está registrado.'

        ]);


        // =========================
        // GUARDAR USUARIO
        // =========================

        DB::table('users')->insert([

            'nombre' => $request->nombre,
            'apellido' => $request->apellido,

'sexo' => $request->sexo,

'edad' => $request->edad,

            'correo' => $request->correo,

            'password' => Hash::make($request->password),

            'terapeuta' => $request->has('terapeuta') ? 1 : 0,

            'created_at' => now(),

            'updated_at' => now()

        ]);


        // =========================
        // REDIRECCIÓN
        // =========================

    return redirect('/registro')
    ->with('success', 'Usuario registrado exitosamente.');

    }

    public function login(Request $request)
    {
        // =========================
        // VALIDAR CAMPOS
        // =========================

        $request->validate([

            'email' => 'required|email',

            'password' => 'required'

        ]);


        // =========================
        // BUSCAR USUARIO
        // =========================

        $usuario = DB::table('users')

            ->where('correo', $request->email)

            ->first();


        // =========================
        // VALIDAR EXISTENCIA
        // =========================

        if (!$usuario)
        {

            return back()

                ->withErrors([

                    'email' =>
                        'El usuario no existe.'

                ]);

        }


        // =========================
        // VALIDAR PASSWORD
        // =========================

        if (!Hash::check(
            $request->password,
            $usuario->password
        ))
        {

            return back()

                ->withErrors([

                    'password' =>
                        'La contraseña es incorrecta.'

                ]);

        }


        // =========================
        // REDIRECCIÓN POR ROL
        // =========================
        // =========================
// GUARDAR SESIÓN
// =========================

// Regenerar sesión para asegurar la persistencia en el navegador (Evita que se pierda)
$request->session()->regenerate();

session([

    'usuario_id' => (int) $usuario->id,

    'usuario_nombre' => $usuario->nombre,

    'usuario_apellido' => $usuario->apellido,

    'usuario_correo' => $usuario->correo,

    'usuario_terapeuta' => (int) $usuario->terapeuta

]);

        if ($usuario->terapeuta)
        {

            return redirect('/terapeuta');

        }


        return redirect('/dashboard');

    }

    public function logout()
    {

        // destruir toda la sesión
        session()->flush();

        // volver al login
        return redirect('/login');

    }
    public function generarPin()
{

    // usuario actual
    $usuarioId = session('usuario_id');


    // generar PIN aleatorio
    $pin = rand(100000, 999999);


    // guardar en BD
    DB::table('users')

        ->where('id', $usuarioId)

        ->update([

            'codigo_vinculacion' => $pin

        ]);


    // regresar a terapeuta
    return back()

        ->with(

            'pin_generado',

            $pin

        );

}

}

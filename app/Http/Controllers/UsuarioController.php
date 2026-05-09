<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function registrar(Request $request)
    {
        User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'terapeuta' => $request->has('terapeuta'),
        ]);

        return redirect('/')->with('success', 'Usuario registrado correctamente');
    }
}
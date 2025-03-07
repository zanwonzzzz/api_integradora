<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //desactivar cuenta
    public function DesactivarCuenta(int $id = 0)
    {

        $user = User::where('id',$id)->first();
        //dd($user);
        if(!$user)
        {
            return response()->json('Ese usuario no existe',422);
        }
        if($user->role_id == 3){
            return response()->json(['msg' => 'No se puede desactivar a un admin'], 403);
        }

        $user->cuenta_activa = false;
        $user->save();

        return response()->json(['message' => 'EL admin ha desactivado la Cuenta del usuario '.$user->name],200);
    }

    //consultas del admin 
    //datos del usuario
    public function DatosUsuario(int $id = 0)
    {
      $user = User::where('id',$id)->first();

      return response()->json([
        "username" => $user->name,
        "email" => $user->email]);

    }
    //datos de los monitores del usuario

    public function UsuariosTodos()
    {
        $usuarios = User::All();
        return response()->json($usuarios,200);
    }

    public function UsuariosActivos()
    {
        $usuarios = User::where('cuenta_activa', 1)->get();
        return response()->json($usuarios, 200);
    }

    public function UsuariosInactivos()
    {
        $usuarios = User::where('cuenta_activa', 1)->get();
        return response()->json($usuarios, 200);
    }
}

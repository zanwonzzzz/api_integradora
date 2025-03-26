<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Monitor;

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
        if($user->rol_id == 3){
            return response()->json(['msg' => 'No se puede desactivar a un admin'], 403);
        }

        $user->cuenta_activa = false;
        $user->save();

        return response()->json(['message' => 'EL admin ha desactivado la Cuenta del usuario '.$user->name],200);
    }

    public function ActivarCuenta(int $id = 0)
    {

        $user = User::where('id',$id)->first();
        //dd($user);
        if(!$user)
        {
            return response()->json('Ese usuario no existe',422);
        }
        if($user->rol_id == 3){
            return response()->json(['msg' => 'No se puede activar a un admin'], 403);
        }

        $user->cuenta_activa = true;
        $user->save();

        return response()->json(['message' => 'EL admin ha activado la Cuenta del usuario '.$user->name],200);
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

    // Historial de monitores: mostrar los monitores que han dado de baja los usuarios. (ultima semana)
    public function MonitoresEliminados()
{
    $fechaInicioSemana = now()->subWeek();

    $monitoresEliminados = Monitor::onlyTrashed()
        ->where('deleted_at', '>=', $fechaInicioSemana)
        ->get();
    
    $monitoresFormateados = $monitoresEliminados->map(function ($monitor) {
        $usuario = User::find($monitor->user_id);
        $nombreUsuario = $usuario ? $usuario->name : 'Usuario no encontrado';
        
        $monitorData = $monitor->toArray();
        
        $monitorData['user_id'] = $nombreUsuario;
        
        return $monitorData;
    });

    return response()->json($monitoresFormateados, 200);
    }

    //Historial de monitores con más actividad semanal
    public function MonitoresActivos()
    {
        $monitores = Monitor::where('Activo','>=',3)->get();
        return response()->json($monitores,200);
    }

    //Historial de monitores con menos actividad semanal
    public function MonitoresMenosActivos()
    {
        $monitores = Monitor::where('Activo','<=',2)->get();
        return response()->json($monitores,200);
    }

    public function MonitoresConMasActividad()
    {
        $fechaInicio = now()->subWeek();
        $fechaFin = now();
        
        $monitores = Monitor::orderBy('Bocina', 'desc')->get();
        
        $monitoresFormateados = $monitores->map(function ($monitor) use ($fechaInicio, $fechaFin) {
            $usuario = User::find($monitor->user_id);
            $nombreUsuario = $usuario ? $usuario->name : 'Usuario no encontrado';
            
            $nivelActividad = 'Normal';
            if ($monitor->Bocina >= 5) {
                $nivelActividad = 'Precaución';
            } elseif ($monitor->Bocina >= 10) {
                $nivelActividad = 'Peligro';
            }
            
            return [
                'id' => $monitor->id,
                'user_id' => $nombreUsuario,
                'Nombre_Monitor' => $monitor->Nombre_Monitor,
                'Ubicacion' => $monitor->Ubicacion,
                'Activo' => $monitor->Bocina,
                'clasificacion' => $nivelActividad,
                'created_at' => $monitor->created_at,
                'updated_at' => $monitor->updated_at,
                'periodo_analisis' => [
                    'desde' => $fechaInicio->format('Y-m-d'),
                    'hasta' => $fechaFin->format('Y-m-d'),
                ]
            ];
        });

        return response()->json([
            'total_monitores' => $monitoresFormateados->count(),
            'periodo_analisis' => [
                'desde' => $fechaInicio->format('Y-m-d'),
                'hasta' => $fechaFin->format('Y-m-d'),
            ],
            'monitores' => $monitoresFormateados
        ], 200);
    }

    public function MonitoresConPromedio()
    {
        $usuarios = User::all();
        
        $usuariosFormateados = [];
        
        foreach ($usuarios as $usuario) {
            $monitores = Monitor::where('user_id', $usuario->id)->get();
            
            if ($monitores->count() > 0) {
                $monitoresData = [];
                
                foreach ($monitores as $monitor) {
                    $monitoresData[] = [
                        'id' => $monitor->id,
                        'nombre' => $monitor->Nombre_Monitor,
                        'ubicacion' => $monitor->Ubicacion,
                        'conteo_bocina' => $monitor->Bocina ?? 0
                    ];
                }
                
                $usuariosFormateados[] = [
                    'id' => $usuario->id,
                    'nombre' => $usuario->name,
                    'email' => $usuario->email,
                    'total_monitores' => $monitores->count(),
                    'monitores' => $monitoresData
                ];
            }
        }
        
        return response()->json([
            'total_usuarios_con_monitores' => count($usuariosFormateados),
            'usuarios' => $usuariosFormateados
        ], 200);
    }

}

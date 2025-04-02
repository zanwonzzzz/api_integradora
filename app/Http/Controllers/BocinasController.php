<?php

namespace App\Http\Controllers;

use App\Models\Bocina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Monitor;
use App\Events\BocinaMostrar;

class BocinasController extends Controller
{
    public function obtenerEstado()
    {
        $estadoBocina = Bocina::orderBy('_id', 'desc')->first();
        
        if (!$estadoBocina) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información sobre el estado de la bocina'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'estado' => $estadoBocina->Estado,
            '_id' => (string)$estadoBocina->_id 
        ], 200);
    }

    public function cambiarEstado(Request $request)
    {
        
        
        event(new BocinaMostrar($request->estado));

        if($request->estado == 0){
            $bocinaEstado = Bocina::create([
                'Estado' => (int)$request->estado,
                'fecha_actualizacion' => now()->toDateTimeString()
            ]);
        } else {

            $monitor = Monitor::find($request->id_monitor);
            $monitor->Bocina++;
            $monitor->save();
            
           

            $bocinaEstado = Bocina::create([
                'Estado' => (int)$request->estado,
                'id_monitor' => $request->id_monitor,
                'id_user' => $request->id_user,
                'fecha_actualizacion' => now()->toDateTimeString()
            ]);

        }
        
        return response()->json([
            'success' => true,
            'message' => 'Estado de bocina actualizado correctamente',
            'estado' => $bocinaEstado->Estado,
            '_id' => (string)$bocinaEstado->_id
        ], 201);
    }
}
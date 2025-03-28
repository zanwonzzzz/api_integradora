<?php

namespace App\Http\Controllers;

use App\Models\Bocina;
use Illuminate\Http\Request;
use Exception;

class BocinasController extends Controller
{
    public function obtenerEstado()
    {
        try {
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
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cambiarEstado(Request $request)
    {
        try {
            $request->validate([
                'estado' => 'required|integer|in:0,1',
            ]);
            
            $bocinaEstado = Bocina::create([
                'Estado' => (int)$request->estado,
                'fecha_actualizacion' => now()->toDateTimeString()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de bocina actualizado correctamente',
                'estado' => $bocinaEstado->Estado,
                '_id' => (string)$bocinaEstado->_id
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
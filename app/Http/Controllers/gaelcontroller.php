<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Events\SensorDataEvent;

class gaelcontroller extends Controller
{
    // public function obtenerdatosporrequest(Request $request)
    // {
    //     $data = $request->all();
        
    //     $sensorData = SensorData::create($data);

    //     $data_evento = SensorData::where('id_monitor', $request->id_monitor)
    //                       ->orderBy('Fecha', 'desc')
    //                       ->take(60)
    //                       ->get();

    //     event(new SensorDataEvent($data_evento));

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Datos guardados correctamente',
    //         'data' => $sensorData
    //     ], 201);
    // }

    public function obtenerdatosporrequest(Request $request)
    {
        $jsonData = $request->json()->all();
        
        $createdRecords = [];
        
        if (isset($jsonData[0]) && is_array($jsonData[0])) {
            foreach ($jsonData as $data) {
                $sensorData = SensorData::create($data);
                $createdRecords[] = $sensorData;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Se guardaron ' . count($createdRecords) . ' registros correctamente',
                'count' => count($createdRecords)
            ], 201);
        } 
        else {
            $data = $request->all();
            $sensorData = SensorData::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Datos guardados correctamente',
                'data' => $sensorData
            ], 201);
        }
    }

    public function obtener_ultimo_valor(Request $request)
    {
        $id_monitor = $request->id_monitor;
        
        $last_data = SensorData::where('id_monitor', $id_monitor)
                        ->orderBy('Fecha', 'desc')
                        ->first();
        
        if (!$last_data) {
            return response()->json(null, 404);
        }
        
        $docArray = is_array($last_data) ? $last_data : $last_data->toArray();
        
        $resultado = [];
        
        $prefijos = ['TEM', 'PIR', 'SON', 'GAS', 'LUZ'];
        
        foreach ($prefijos as $prefijo) {
            foreach ($docArray as $key => $value) {
                if (is_string($key) && strpos($key, $prefijo) === 0) {
                    $resultado[] = $prefijo . ":" . $value;
                    break;
                }
            }
        }
        
        $respuesta = implode(',', $resultado);
        
        return response()->json($respuesta);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Events\SensorDataEvent;

class gaelcontroller extends Controller
{
    public function obtenerdatosporrequest(Request $request)
    {
        $data = $request->all();
        
        $sensorData = SensorData::create($data);

        $data_evento = SensorData::where('id_monitor', $request->id_monitor)
                          ->orderBy('Fecha', 'desc')
                          ->take(60)
                          ->get();

        event(new SensorDataEvent($data_evento));

        return response()->json([
            'success' => true,
            'message' => 'Datos guardados correctamente',
            'data' => $sensorData
        ], 201);
    }
}
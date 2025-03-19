<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;

class gaelcontroller extends Controller
{
    public function obtenerdatosporrequest(Request $request)
    {
        $data = $request->all();
        
        $sensorData = SensorData::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Datos guardados correctamente',
            'data' => $sensorData
        ], 201);
    }
}
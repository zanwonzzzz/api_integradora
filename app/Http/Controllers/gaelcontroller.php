<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;

class gaelcontroller extends Controller
{
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
}
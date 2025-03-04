<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\SensorDataEvent;
use App\Models\DataSensor;
use Illuminate\Support\Facades\Validator;

class SensorDataController extends Controller
{
    public function index($idsensor)
    {
        $data = DataSensor::where('sensor_id', $idsensor)
                          ->orderBy('created_at', 'desc')
                          ->take(60)
                          ->get();        
        event(new SensorDataEvent($data));
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'required|integer',
            'valor' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = new DataSensor();
        $data->sensor_id = $request->sensor_id;
        $data->valor = $request->valor;
        $data->save();

        $dataevento = DataSensor::where('sensor_id', $request->sensor_id)
                          ->orderBy('created_at', 'desc')
                          ->take(60)
                          ->get();        
        event(new SensorDataEvent($dataevento));

        return response()->json($data);
    }
}

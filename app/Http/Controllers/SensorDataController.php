<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\SensorDataEvent;
use App\Models\DataSensor;
use Illuminate\Support\Facades\Validator;
use App\Models\SensorData;

class SensorDataController extends Controller
{
    // public function index($idsensor)
    // {
    //     $data = DataSensor::where('sensor_id', $idsensor)
    //                       ->orderBy('created_at', 'desc')
    //                       ->take(60)
    //                       ->get();        
    //     event(new SensorDataEvent($data));
    //     return response()->json($data);
    // }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'sensor_id' => 'required|integer',
    //         'valor' => 'required|integer',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $data = new DataSensor();
    //     $data->sensor_id = $request->sensor_id;
    //     $data->valor = $request->valor;
    //     $data->save();

    //     $dataevento = DataSensor::where('sensor_id', $request->sensor_id)
    //                       ->orderBy('created_at', 'desc')
    //                       ->take(60)
    //                       ->get();        
    //     event(new SensorDataEvent($dataevento));

    //     return response()->json($data);
    // }

    public function obtenerDataSensor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor' => 'required|string',
            'id_monitor' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sensor = $request->sensor;
        $id_monitor = $request->id_monitor;
        $concatenated = $sensor . $id_monitor;

        $data = SensorData::where($concatenated, '!=', null)
                        ->orderBy('Fecha', 'desc')
                        ->get(['Fecha', $concatenated])
                        ->makeHidden('_id');

        $data_evento = SensorData::where($concatenated, '!=', null)
                        ->orderBy('Fecha', 'desc')
                        ->take(60)
                        ->get(['Fecha', $concatenated])
                        ->makeHidden('_id');
        
        event(new SensorDataEvent($data_evento));

        return response()->json($data);
    }
}

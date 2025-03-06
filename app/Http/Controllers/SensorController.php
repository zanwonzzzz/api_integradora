<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use Illuminate\Support\Facades\Validator;

class SensorController extends Controller
{
    public function agregarsensor(request $request){

        $validator = Validator::make($request->all(), [
            'sensor' => 'required|string|max:100'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        }

        $sensor = new Sensor();
        $sensor->Nombre_Sensor = $request->sensor;
        $sensor->save();

        $sendToMongoController = new SendToMongoDataController();
        $sendToMongoController->sendSensorToMongo(new Request([
            'id' => $sensor->id,
            'Nombre_Sensor' => $sensor->Nombre_Sensor
        ]));
    }

    public function obtenersensores(){
        $sensor = Sensor::all();
        return response()->json([
            'msg' => 'Sensores Disponibles',
            'data' => $sensor
        ], 200);
    }

    public function eliminarsensor(int $id=0){
        $sensor = Sensor::find($id);
        $sensor->delete();
    }
}

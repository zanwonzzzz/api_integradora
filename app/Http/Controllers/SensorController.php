<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;

class SensorController extends Controller
{
    public function agregarsensor(request $request){

        $sensor = new Sensor();
        $sensor->Nombre_Sensor = $request->sensor;
        $sensor->save();

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

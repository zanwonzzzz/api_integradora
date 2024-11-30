<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;
use App\Models\Sensor;

class MonitorController extends Controller
{
    public function crearm_s(request $request){
        $id = auth()->user()->id;

        $monitor = new Monitor();
        $monitor->user_id = $id;
        $monitor->Nombre_Monitor = $request->nombre;
        $monitor->save();

    }

    public function elegir_sensores(int $id=0){

        $monitor = auth()->user()->monitor()->first();
        $sensor_id = Sensor::find($id);
        //if($sensor_id > 0){
            $monitor->sensores()->attach($sensor_id);
       /*  }
        else {
            return response()->json(['message' => 'sensor no encontrado']);
        } */
       

    }

}

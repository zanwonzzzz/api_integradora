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
}

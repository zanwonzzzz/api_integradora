<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sensor;
use Illuminate\Support\Collection;
use App\Models\InfoSensor;
class AdafruitController extends Controller
{
    public function obtener(int $id =0){

        $sensor  = Sensor::find($id);
        $key = config('services.adafruit.key');
        //dd($key);
        //$response = Http::get('https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.gas/data');

        $response = Http::withUrlParameters([
            'endpoint' => 'https://io.adafruit.com/api/v2/TomasilloV/feeds',
            'sensor' => 'sensores.'.$sensor->Nombre_Sensor,
            'page' => 'data',
        ])->get('{+endpoint}/{sensor}/{page}/');

        
       // dd($response->json());
        
        if($response->successful()){
            $data = $response->json();
           //$coleccion = collect($data);
          // dd($coleccion);
            
            return response()->json($data);
            //return 'oka';
                
            
            
        }
    }


    //mandar datos de android a la api y despues a adafruit

    public function crear(request $request){
        $valor = new InfoSensor();
        $valor->monitor_sensor = $request->monitor_sensor;
        $valor->valor = $request->valor;
        $valor->save();
/* 
        $key = config('services.adafruit.key');

        $response = Http::WithHeaders([
            'X-AIO-Key' => $key])->post('https://io.adafruit.com/api/v2/TomasilloV/feeds/Sensores_Proyecto/data', [
                'name_sensor' => $request->name,
                'value' => $request->value
            ]);

            if ($response->successful()) {
                return response()->json(['message' => 'datos enviados correctamente']);
            }  */
    }

    //silenciar bocina
}

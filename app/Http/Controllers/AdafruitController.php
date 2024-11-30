<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sensor;
use Illuminate\Support\Collection;
class AdafruitController extends Controller
{
    public function obtener(){

        $key = config('services.adafruit.key');
        $nombresensor = 'gas';
 
        //dd($key);
        $response = Http::get('https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.gas/data');

        //dd($response->json());
        
        if($response->successful()){
            $data = $response->json();
           //$coleccion = collect($data);
          // dd($coleccion);
            
            return response()->json($data);
            //return 'oka';
                
            
            
        }
    }

    public function crear(request $request){
        $led = new Sensor();
        $led->name_sensor = $request->name;
        $led->value = $request->value;
        $led->save();

        $key = config('services.adafruit.key');

        $response = Http::WithHeaders([
            'X-AIO-Key' => $key]) ->post('https://io.adafruit.com/api/v2/TomasilloV/feeds/Sensores_Proyecto/data', [
                'name_sensor' => $request->name,
                'value' => $request->value
            ]);

            if ($response->successful()) {
                return response()->json(['message' => 'datos enviados correctamente']);
            } 
    }
}

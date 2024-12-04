<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sensor;
use Illuminate\Support\Collection;
use App\Models\InfoSensor;
use App\Controllers\MonitorController;
use App\Models\Monitor;
use App\Models\MonitorSensor;
use Illuminate\Support\Facades\Validator;
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

    //RESUMEN DE LOS DATOS DE LOS SENSORES DE  7 DIAS

    //DATOS EN VIVO LAST DATA
    public function Envivo(int $idmonitor=0){
       $adafruitsensores = [];
       $key = config('services.adafruit.key');
       /*  $sensor  = Sensor::find($id);
        $key = config('services.adafruit.key');

        $response = Http::withUrlParameters([
            'endpoint' => 'https://io.adafruit.com/api/v2/TomasilloV/feeds',
            'sensor' => 'sensores.'.$sensor->Nombre_Sensor,
            'page' => 'data',
            'ultimo' => 'last'
        ])->get('{+endpoint}/{sensor}/{page}/{ultimo}'); */

         
        $id = auth()->user()->id;
        $monitor = Monitor::find($idmonitor);
        $monitorsensor = MonitorSensor::where('monitor_id', $monitor->id)->pluck('sensor_id');
        $sensores = Sensor::whereIn('id', $monitorsensor)->pluck('id');
            
        $adafruitsensores = $sensores->toArray();
        $resultado = [];
        foreach($adafruitsensores as $adafruitsensor){
           $sensor = Sensor::find($adafruitsensor);
        

           $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensor->Nombre_Sensor}/data", [
            'limit' => 1,
            'order' => 'desc',
        ]);
        
      

        if($response->successful()){
            $data = $response->json();
            $resultado[] = [
                'sensor' => $sensor->Nombre_Sensor,
                'data' => $data,
            ];
           
                
            
            
        }
      } 
      return response()->json($resultado);


    }

    public function ApagarBocina(request $request){

        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }


        $value = $request->value;
        $key = config('services.adafruit.key');
        //dd($key);
        //dd($valor);

        $response = Http::WithHeaders([
            'X-AIO-Key' => $key])->post('https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data', [
                'value' => $value
                
            ]);

            //dd($response);

            if ($response->successful()) {
                return response()->json(['message' => 'datos enviados correctamente']);
            } else {
               
                return response()->json(['message' => 'Error al enviar los datos'], 500);
            }
    }


    //MANDAR LOS SENSORES DE UN MONITOR A ADAFRUIT
    public function AdafruitSensor(int $idmonitor=0){

        $adafruitsensores = [];
       $key = config('services.adafruit.key');
         
        $id = auth()->user()->id;
        $monitor = Monitor::find($idmonitor);
        $monitorsensor = MonitorSensor::where('monitor_id', $monitor->id)->pluck('sensor_id');
        $sensores = Sensor::whereIn('id', $monitorsensor)->pluck('id');
            
        $adafruitsensores = $sensores->toArray();
        $resultado = [];
        
       
        

           $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
            'value' => implode(',', $adafruitsensores)
        ]);
        
    }
}

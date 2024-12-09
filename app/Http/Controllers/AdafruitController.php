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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Estado;
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
            'limit' => 10,
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

    public function UltimoDato(int $idmonitor=0){
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


    public function Promedio(){
        $key = config('services.adafruit.key');
        //$sensor  = Sensor::find($id);
        
        $fechaactual = Carbon::now();
        $cada5dias = [];
        $promedios = [];
        $estado = "";
        $idestado = 0;

        $dias = 5;
        for($i=0; $i < $dias; $i++){

            $contador = $i * 1;
            $fechalimite = Carbon::now()->subDays($contador)->startOfDay()->utc();
            $fechafinal =Carbon::now()->subDays($contador)->endOfDay()->utc();

            $response = Http::withHeaders([
                'X-AIO-Key' => $key,  
            ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.gas/data?start_time={$fechalimite}&end_time={$fechafinal}");
             
            $data = $response->json(); 
            $mismosdias= [];
             
    
            foreach($data as $res){
                $fecha = Carbon::parse($res["created_at"])->utc();
                \Log::info('Fecha procesada:', [
                    'original' => $res['created_at'],
                    'carbon' => $fecha,
                    'esMismoDia' => $fecha->isSameDay($fechalimite),
                    'fechalimite' => $fechalimite
                ]);
              // dd($fecha);
    
                if($fecha->isSameDay($fechalimite)){

                    $mismosdias[] = 
                    [
                        "fecha" => $res["created_at"],
                       "valor" => $res["value"]
                    ];
                    /* explode(" ", $mismosdias); */
                }
            } 
          
            if (!empty($mismosdias)) {
                $valores = array_column($mismosdias, 'valor');
                $promedio = array_sum($valores) / count($valores);

                //ESTOS DATOS POR MIENTRAS PQ SON DEL DE GAS
                if($promedio >= 800){
                    $estado = "triste";
                    $idestado = 3;
                }
                else if($promedio < 800 && $promedio >= 400){
                    $estado = "serio";
                    $idestado = 2;
                }
                else {
                    $estado = "feliz";
                    $idestado = 1;
                }
            } else {
                $promedio = 0;
            }
    
            
             $resultados[] = [
                "fecha" => $fechalimite->toDateString(),
                "promedio" => $promedio,
                "estado" => $estado,
                "idestado" => $idestado
            ]; 
            

            /* $cada5dias[] = [
                'fechalimite' => $fechalimite,
                'fechafinal' => $fechafinal,
            ]; */
        }

       
        /* foreach($cada5dias as $cada5dia){ */

           /*  $fechalimite = $cada5dia['fechalimite'];
            $fechafinal = $cada5dia['fechafinal'];
 */
            
          
           
    
       // }

       /*  rsort($mismosdias);
    
        $mayor = $mismosdias[0];
        $menor = end($mismosdias);
        $promedio = ($mayor + $menor)/2;  */
        

       
        return response()->json([
            'resultados' => $resultados
        ]);
    }


    public function PromedioHora(){
        
    }
}

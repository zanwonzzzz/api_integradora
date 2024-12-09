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
use Illuminate\Support\Facades\DB;
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

        /*
         {'value': 'apagar'}
        */
        $validator = Validator::make($request->all(), [
            'value' => 'required|string',
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

    public function ConsultarEstadoBocina(){
        $key = config('services.adafruit.key');
        

        $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.estado-bocina/data", [
            'limit' => 1,
            'order' => 'desc',
        ]);
        
        /* dd($response); */

            if ($response->successful()) {
                $data = $response->json();

                foreach($data as $res){
                    $data = $res['value'];
                }

               

                
                     if($data == 1){
                        return response()->json(['message' => 'bocina encendida']);
                    }
                    else if($data == 0){
                        return response()->json(['message' => 'bocina apagada']);
                    }
                 

                
               
            } else {
                return response()->json(['message' => 'Error al enviar los datos'], 500);
            }
    }


    //MANDAR LOS SENSORES DE UN MONITOR A ADAFRUIT
    public function AdafruitSensor(){

        $adafruitsensores = [];
       $key = config('services.adafruit.key');
         
        $id = auth()->user()->id;
        $monitores = Monitor::where('user_id', $id)->pluck('id');


       /*  $monitorsensor = MonitorSensor::whereIn('monitor_id', $monitores)->pluck('sensor_id');


         $sensores = Sensor::whereIn('id', $monitorsensor)->pluck('id');
        
         $adafruitsensores = $sensores->toArray();
         //dd($adafruitsensores);
       
      
         return $adafruitsensores; */

         $monitorsensor = MonitorSensor::whereIn('monitor_id', $monitores)
        ->get(['monitor_id', 'sensor_id']);

   
    foreach ($monitorsensor as $monitorSensor) {
        $adafruitsensores[] = $monitorSensor->sensor_id;
    }

   
    return $adafruitsensores;

           /* $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
            'value' => implode(',', $adafruitsensores)
        ]); */


        
    }

    public function SensorAda(){

        $adafruitsensores = [];
       $key = config('services.adafruit.key');
         
        $id = auth()->user()->id;
        $monitores = Monitor::where('user_id', $id)->pluck('id');

/* 
        $monitorsensor = MonitorSensor::whereIn('monitor_id', $monitores)->pluck('sensor_id');


         $sensores = Sensor::whereIn('id', $monitorsensor)->pluck('id');
        
         $adafruitsensores = $sensores->toArray(); */
         //dd($adafruitsensores);
       
         $monitorsensor = MonitorSensor::whereIn('monitor_id', $monitores)->get(['sensor_id']);

         // Convierte a un array y extrae solo los IDs de sensores
         foreach ($monitorsensor as $monitorSensor) {
             $adafruitsensores[] = $monitorSensor->sensor_id;
         }
         
      
         

           $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
            'value' => implode(',', $adafruitsensores)
        ]);


        
    }

    public function BorrarSensores($idmonitor){
        $adafruitsensores = [];
        $key = config('services.adafruit.key');
          
         $id = auth()->user()->id;
         $monitor = Monitor::where('user_id', $id)->where('id', $idmonitor)->first();
 
 
         $monitorsensor = MonitorSensor::whereIn('monitor_id', $monitor)->pluck('sensor_id');
 
 
          $sensores = Sensor::whereIn('id', $monitorsensor)->pluck('id');
         
          $adafruitsensores = $sensores->toArray();
          dd($adafruitsensores);

           
         
 
            /* $response = Http::withHeaders([
             'X-AIO-Key' => $key,  
         ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
             'value' => implode(',', $adafruitsensores)
         ]);  */

         return $adafruitsensores; 
        
    }


    public function Promedio(int $idsensor=0){
        $key = config('services.adafruit.key');
        
        
        $fechaactual = Carbon::now();
        $ada = new AdafruitController();
        $cada5dias = [];
        $promedios = [];
        $estado = "";
        $idestado = 0;
       $sensorespromedios = [1,2,4,5];

       $movimientosmuchos = 0;
        $dias = 6;
        for($i=1; $i < $dias; $i++){

            $sensor= Sensor::find($idsensor);
           

            $contador = $i * 1;
            $fechalimite = Carbon::now()->subDays($contador)->startOfDay()->utc();
            $fechafinal =Carbon::now()->subDays($contador)->endOfDay()->utc();

            

            $response = Http::withHeaders([
                'X-AIO-Key' => $key,  
            ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensor->Nombre_Sensor}/data?start_time={$fechalimite}&end_time={$fechafinal}");
             
            $data = $response->json(); 
            $mismosdias= [];
            //dd($sensor->Nombre_Sensor);
            Log::info('Sensor procesado:', ['id' => $idsensor, 'nombre' => $sensor->Nombre_Sensor]);

    
            foreach($data as $res){
                $fecha = Carbon::parse($res["created_at"])->utc();
                /* \Log::info('Fecha procesada:', [
                    'original' => $res['created_at'],
                    'carbon' => $fecha,
                    'esMismoDia' => $fecha->isSameDay($fechalimite),
                    'fechalimite' => $fechalimite
                ]); */
              // dd($fecha);
    
                if($fecha->isSameDay($fechalimite)){

                    if(in_array($idsensor, $sensorespromedios)){
                        
                    $mismosdias[] = 
                    [
                        "fecha" => $res["created_at"],
                       "valor" => $res["value"]
                    ];
                    }
                    else if($idsensor === 3){
                        if($res["value"] == 1){
                            $mismosdias[] =[
                                "fecha" => $res["created_at"],
                               "valor" => $res["value"]
                            ];
                        }
                        
                    }

                    /* explode(" ", $mismosdias); */
                }
            } 

          
            if (!empty($mismosdias)) {

                if($idsensor === 3){

                    

                    $movimientosmuchos = count($mismosdias);

                    if($movimientosmuchos >= 100){
                        $estado = "Mucho Movimiento";
                        $idestado = 1;
                        
                    }
                    else if($movimientosmuchos >= 40 && $movimientosmuchos < 100){
                        $estado = "Movimiento";
                        $idestado = 2;
                    }
                    else if($movimientosmuchos >= 0 && $movimientosmuchos < 40){
                        $estado = "Poco Movimiento";
                        $idestado = 3;

                    }
                  
                    
                }

               else if(in_array($idsensor, $sensorespromedios)){
                    
                
                $valores = array_column($mismosdias, 'valor');
                $promedio = array_sum($valores) / count($valores);
                

                if($idsensor === 1){
                   $result = $ada->GasComparacion($promedio,$estado,$idestado);
                }
                else if($idsensor === 2){
                    $result = $ada->TemperaturaComparacion($promedio,$estado,$idestado);
                }
                else if($idsensor === 4){
                    $result = $ada->SonidoComparacion($promedio,$estado,$idestado);
                }
                else if($idsensor === 5){
                    $result = $ada->LuzComparacion($promedio,$estado,$idestado);
                }

                $estado = $result['estado'];
                $idestado = $result['idestado'];
              }
                

            } else {
                $promedio = 0;
            }
    
            
            if ($idsensor === 3) {
                $resultados[] = [
                    "fecha" => $fechalimite->toDateString(),
                    "promedio" => $movimientosmuchos,
                    "estado" => $estado,
                    "idestado" => $idestado
                ];

            } else {
                $resultados[] = [
                    "fecha" => $fechalimite->toDateString(),
                    "promedio" => $promedio,
                    "estado" => $estado,
                    "idestado" => $idestado
                ];
            }
            

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


    

    

    public function GasComparacion(int $promedio = 0,$estado = "",$idestado = 0){

        
        
                //ESTOS DATOS POR MIENTRAS PQ SON DEL DE GAS
                 if($promedio >= 700 && $promedio < 1023){
                    $estado = "Calidad de Aire Mala";
                    $idestado = 3;
                }
                else if($promedio >= 400 && $promedio < 700){
                    $estado = "Calidad de Aire Normal";
                    $idestado = 2;
                }
                else if($promedio >= 0 && $promedio < 400){
                    $estado = "Calidad de Aire Buena";
                    $idestado = 1;
                } 

                return ['estado' => $estado, 'idestado' => $idestado];


    }

    public function TemperaturaComparacion(int $promedio = 0,$estado = "",$idestado = 0){

        if($promedio >= 36 && $promedio < 100 ){
            $estado = "Temperatura Alta";
            $idestado = 3;
        }
        else if($promedio >= 0 && $promedio <= 20){
            $estado = "Temperatura Baja";
            $idestado = 3;
        }
        else if($promedio >= 21 && $promedio <= 35){
            $estado = "Temperatura Normal";
            $idestado = 1;
        }
        

        return ['estado' => $estado, 'idestado' => $idestado];

    }

    public function SonidoComparacion(int $promedio = 0,$estado = "",$idestado = 0){

        

        if($promedio >= 800 && $promedio < 1023){
            $estado = "Sonido Alto";
            $idestado = 3;
        }
        else if($promedio >= 500 && $promedio < 800){
            $estado = "Sonido Normal";
            $idestado = 1;
        }
        else if($promedio >= 0 && $promedio < 500){
            $estado = "Sonido Bajo";
            $idestado = 2;
        } 

        return ['estado' => $estado, 'idestado' => $idestado];

    }

    public function LuzComparacion(int $promedio = 0,$estado = "",$idestado = 0){

       

        if($promedio >= 400){
            $estado = "Nivel de Luz Alto";
            $idestado = 3;
        }
        else if($promedio >= 50 && $promedio < 400){
            $estado = "Nivel de Luz Normal";
            $idestado = 1;
        }
        else if($promedio >= 0 && $promedio < 50){
            $estado = "Nivel de Luz Bajo";
            $idestado = 2;
        } 

        return ['estado' => $estado, 'idestado' => $idestado];

    }


    public function PromedioPorHora(int $idsensor = 0, $fechalimite = ""){

        $key = config('services.adafruit.key');
        
        
        $fechaactual = Carbon::now();
        $ada = new AdafruitController();
        $cada5dias = [];
        $promedios = [];
        $estado = "";
        $idestado = 0;
       


            $sensor= Sensor::find($idsensor);
           

            /* $contador = $i * 1; */ 
            $fechafinal = $fechalimite;
            $fechalimite =Carbon::parse($fechalimite)->startOfDay()->utc();
            $fechafinal =Carbon::parse($fechalimite)->endOfDay()->utc();

           /*  dd($fechalimite,$fechafinal); */

            /* $horalimite = Carbon::now()->subDays($contador)->format('H:i:s');
            $horafinal =Carbon::now()->subDays($contador)->format('H:i:s'); */

            $response = Http::withHeaders([
                'X-AIO-Key' => $key,  
            ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensor->Nombre_Sensor}/data",[
            'start_time' => $fechalimite->toIso8601String(),
            'end_time' => $fechafinal->toIso8601String(),
         ]);

        /* dd($fechalimite->toIso8601String(),$fechafinal->toIso8601String()); */
             
            $data = $response->json(); 
            $mismosdias= [];
            $mismashoras= [];
            //dd($sensor->Nombre_Sensor);
            Log::info('Sensor procesado:', ['id' => $idsensor, 'nombre' => $sensor->Nombre_Sensor]);

    
            foreach($data as $res){
                $fecha = Carbon::parse($res["created_at"])->utc();
    
                if($fecha->isSameDay($fechalimite)){

                    
                    
                  $mismosdias[] = 
                    [
                        "fecha" => $fecha,
                       "valor" => $res["value"]
                    ]; 
                   
                }
            } 

            $promediosPorHora = [];
          
            for ($hora = 0; $hora < 24; $hora++) {
                $totalValores = 0;
                $cantidadValores = 0;
    
                foreach ($mismosdias as $item) {
                    $horaItem = $item['fecha']->hour; 
                    if ($horaItem === $hora) {
                        $totalValores += $item['valor'];
                        $cantidadValores++;
                    }
                }
    
                
                $promediosPorHora[$hora] = $cantidadValores > 0 ? $totalValores / $cantidadValores : 0;
            }
    
            $resultados[] = [
                "fecha" => $fechalimite->toDateString(),
                "promedios_por_hora" => $promediosPorHora,
            ];

            /* $cada5dias[] = [
                'fechalimite' => $fechalimite,
                'fechafinal' => $fechafinal,
            ]; */
        

       
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

    public function MovimientoComparacion(){

        $key = config('services.adafruit.key');
        $mismosdias = [];
        $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.movimiento/data?start_time=2024-12-07T00:00:00&end_time=2024-12-07T11:59:59");
    
         
        $data = $response->json(); 
        
        
        foreach($data as $res){
           
            $createdAt = \Carbon\Carbon::parse($res['created_at']);

          
            if($res["value"] == 1){
                $mismosdias[] = 
                [
                    "fecha" => $createdAt,
                   "valor" => $res["value"]
                ]; 
            };

        } 

        return response()->json([
            'mismosdias' => $mismosdias
        ]);

    }


    public function GasChequeo(){

        $key = config('services.adafruit.key');
        $mismosdias = [];
        $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.gas/data?start_time=2024-12-03T00:00:00&end_time=2024-12-03T11:59:59");
    
         
        $data = $response->json(); 
        
        
        foreach($data as $res){
           
            $createdAt = \Carbon\Carbon::parse($res['created_at']);

          
           
                $mismosdias[] = 
                [
                    "fecha" => $createdAt,
                   "valor" => $res["value"]
                ]; 
            

        } 

        return response()->json([
            'mismosdias' => $mismosdias
        ]);

    }


   public function Promediobd(int $idsensor=0){

    $key = config('services.adafruit.key');
        
        
    $fechaactual = Carbon::now();
    $ada = new AdafruitController();
    $cada5dias = [];
    $promedios = [];
    $estado = "";
    $idestado = 0;
   $sensorespromedios = [1,2,4,5];

   $movimientosmuchos = 0;
    $dias = 6;
    for($i=1; $i < $dias; $i++){

        $sensor= Sensor::find($idsensor);
       

        $contador = $i * 1;
        $fechalimite = Carbon::now()->subDays($contador)->startOfDay()->utc();
        $fechafinal =Carbon::now()->subDays($contador)->endOfDay()->utc();

       

            if(in_array($idsensor, $sensorespromedios)){
                    
                $mismosdias = DB::table('infosensores')
            ->where('sensor_id', $idsensor)
            ->whereBetween('created_at', [$fechalimite, $fechafinal])
            ->get(['created_at', 'valor'])
            ->toArray();
                }
                else if($idsensor === 3){
                    $mismosdias = DB::table('infosensores')
                    ->where('sensor_id', $idsensor)
                    ->whereBetween('created_at', [$fechalimite, $fechafinal])
                    ->where('valor',1)
                    ->get(['created_at', 'valor'])
                    ->toArray();

                   
                }
       /*  $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensor->Nombre_Sensor}/data?start_time={$fechalimite}&end_time={$fechafinal}");
         
        $data = $response->json(); 
        $mismosdias= [];
        //dd($sensor->Nombre_Sensor);
        Log::info('Sensor procesado:', ['id' => $idsensor, 'nombre' => $sensor->Nombre_Sensor]);


        foreach($data as $res){
            $fecha = Carbon::parse($res["created_at"])->utc();
            /* \Log::info('Fecha procesada:', [
                'original' => $res['created_at'],
                'carbon' => $fecha,
                'esMismoDia' => $fecha->isSameDay($fechalimite),
                'fechalimite' => $fechalimite
            ]); 
          // dd($fecha);

            if($fecha->isSameDay($fechalimite)){

                if(in_array($idsensor, $sensorespromedios)){
                    
                $mismosdias[] = 
                [
                    "fecha" => $res["created_at"],
                   "valor" => $res["value"]
                ];
                }
                else if($idsensor === 3){
                    if($res["value"] == 1){
                        $mismosdias[] =[
                            "fecha" => $res["created_at"],
                           "valor" => $res["value"]
                        ];
                    }
                }

                /* explode(" ", $mismosdias); */
            //}
        //}  */

      
        if (!empty($mismosdias)) {

            if($idsensor === 3){

                
                    $movimientosmuchos = count($mismosdias);
                
                
                

                if($movimientosmuchos >= 100){
                    $estado = "Mucho Movimiento";
                    $idestado = 1;
                    
                }
                else if($movimientosmuchos >= 40 && $movimientosmuchos < 100){
                    $estado = "Algo de Movimiento";
                    $idestado = 2;
                }
                else if($movimientosmuchos >= 0 && $movimientosmuchos < 40){
                    $estado = "Poco Movimiento";
                    $idestado = 3;

                }
              
                
            }

           else if(in_array($idsensor, $sensorespromedios)){
                
            
            $valores = array_column($mismosdias, 'valor');
            $promedio = array_sum($valores) / count($valores);
            

            if($idsensor === 1){
               $result = $ada->GasComparacion($promedio,$estado,$idestado);
            }
            else if($idsensor === 2){
                $result = $ada->TemperaturaComparacion($promedio,$estado,$idestado);
            }
            else if($idsensor === 4){
                $result = $ada->SonidoComparacion($promedio,$estado,$idestado);
            }
            else if($idsensor === 5){
                $result = $ada->LuzComparacion($promedio,$estado,$idestado);
            }

            $estado = $result['estado'];
            $idestado = $result['idestado'];
          }
            

        } else {
            $promedio = 0;
        }

        
        if ($idsensor === 3) {
            $resultados[] = [
                "fecha" => $fechalimite->toDateString(),
                "promedio" => $movimientosmuchos,
                "estado" => $estado,
                "idestado" => $idestado
            ];

        } else {
            $resultados[] = [
                "fecha" => $fechalimite->toDateString(),
                "promedio" => round($promedio),
                "estado" => $estado,
                "idestado" => $idestado
            ];
        }
        

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


  
    public function promediobdporhora(int $idsensor = 0, $fechalimite = ""){

        $key = config('services.adafruit.key');
        
        
        $fechaactual = Carbon::now();
        $ada = new AdafruitController();
        $cada5dias = [];
        $promedios = [];
        $estado = "";
        $idestado = 0;
       


            $sensor= Sensor::find($idsensor);
           

            /* $contador = $i * 1; */ 
            /* $fechafinal = $fechalimite; */
            $fechalimite =Carbon::parse($fechalimite)->startOfDay()->utc();
            $fechafinal =Carbon::parse($fechalimite)->endOfDay()->utc();

           /*  dd($fechalimite,$fechafinal); */

            /* $horalimite = Carbon::now()->subDays($contador)->format('H:i:s');
            $horafinal =Carbon::now()->subDays($contador)->format('H:i:s'); */

            /* $response = Http::withHeaders([
                'X-AIO-Key' => $key,  
            ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensor->Nombre_Sensor}/data",[
            'start_time' => $fechalimite->toIso8601String(),
            'end_time' => $fechafinal->toIso8601String(),
         ]);
 */
        /* dd($fechalimite->toIso8601String(),$fechafinal->toIso8601String()); */
             
            /* $data = $response->json(); */ 
            $mismosdias= [];
            $mismashoras= [];
            //dd($sensor->Nombre_Sensor);
            Log::info('Sensor procesado:', ['id' => $idsensor, 'nombre' => $sensor->Nombre_Sensor]);

            $data = DB::table('infosensores')
            ->where('sensor_id', $idsensor)
            ->whereBetween('created_at', [$fechalimite, $fechafinal])
            ->get(['created_at', 'valor']);
           /*  ->toArray(); */

    
           foreach ($data as $res) {
            $fecha = Carbon::parse($res->created_at)->utc();
    
            if ($fecha->isSameDay($fechalimite)) {
                $mismosdias[] = [
                    "fecha" => $fecha,
                    "valor" => $res->valor, 
                ];
            }
        }

            $promediosPorHora = [];
          
            for ($hora = 0; $hora < 24; $hora++) {
                $totalValores = 0;
                $cantidadValores = 0;
    
                foreach ($mismosdias as $item) {
                    $horaItem = $item['fecha']->hour; 
                    if ($horaItem === $hora) {
                        $totalValores += $item['valor'];
                        $cantidadValores++;
                    }
                }
    
                
                $promediosPorHora[$hora] = $cantidadValores > 0 ? round($totalValores / $cantidadValores, 2) : 0;

            }
    
            $resultados[] = [
                "fecha" => $fechalimite->toDateString(),
                "promedios_por_hora" => $promediosPorHora,
            ];

            /* $cada5dias[] = [
                'fechalimite' => $fechalimite,
                'fechafinal' => $fechafinal,
            ]; */
        

       
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

   }



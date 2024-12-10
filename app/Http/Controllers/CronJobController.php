<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sensor;
use App\Models\InfoSensor;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\CronJob; 

class CronJobController extends Controller
{
    public function CronJobParaGuardarDatos()
    {
        $sensores = [1 => "gas", 2 => "temperatura", 3 => "movimiento", 4 => "sonido", 5 => "luz"];
        
        
        $cronJob = CronJob::where('nombre', 'CargaInicial')->first();
    
        
        if (!$cronJob) {
            
            $cronJob = CronJob::create([
                'nombre' => 'CargaInicial',
                'completada' => false 
            ]);
            Log::info("Cron Job 'CargaInicial' creado.");
        }
    
        
        if ($cronJob->completada === false) {

            Log::info("Comenzando la carga inicial de datos...");
            $dias = 6;
    
            for ($i = $dias; $i > 1; $i--) {
                $fechalimite = Carbon::now()->subDays($i)->startOfDay()->utc();
                $fechafinal = Carbon::now()->subDays($i)->endOfDay()->utc();
    
                foreach ($sensores as $sensorId => $sensorNombre) {
                    $key = config('services.adafruit.key');
    
                    $response = Http::withHeaders([
                        'X-AIO-Key' => $key,
                    ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensorNombre}/data?start_time={$fechalimite}&end_time={$fechafinal}");
    
                    $data = $response->json();
    
                    foreach ($data as $res) {
                        $createdAt = \Carbon\Carbon::parse($res['created_at']);
                        
                        
                        DB::table('infosensores')->updateOrInsert([
                            'sensor_id' => $sensorId,
                            'valor' => $res["value"],
                            'created_at' => $createdAt->toDateTimeString(),
                        ]);
                    }
                }
    
                Log::info("Datos procesados para el día: {$fechalimite->toDateString()}");
            }
    
            
            $cronJob->completada = true;
            $cronJob->save();
    
            Log::info("Carga inicial completada.");
        } else {
            
            Log::info("Carga inicial ya completada, comenzando a procesar los datos nuevos.");
    
            
            $fechalimite = Carbon::now()->startOfDay()->utc();
            $fechafinal = Carbon::now()->endOfDay()->utc();
    
            foreach ($sensores as $sensorId => $sensorNombre) {
                $key = config('services.adafruit.key');
    
                
                $response = Http::withHeaders([
                    'X-AIO-Key' => $key,
                ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensorNombre}/data?start_time={$fechalimite}&end_time={$fechafinal}");
    
                $data = $response->json();
    
                foreach ($data as $res) {
                    $createdAt = Carbon::parse($res['created_at']);
                    
                    DB::table('infosensores')->updateOrInsert([
                        'sensor_id' => $sensorId,
                        'valor' => $res["value"],
                        'created_at' => $createdAt->toDateTimeString(),
                    ]);
                }
            }
    
            Log::info("Datos nuevos procesados.");
        }
    }
    
     
    public function CronJobParaDatosNuevos(){

        $sensores = [1 => "gas", 2 => "temperatura",3 => "movimiento",4 => "sonido",5 => "luz"];
       
         
         
         
             $fechalimite = Carbon::now()->startOfDay()->utc(); 
             $fechafinal = Carbon::now()->endOfDay()->utc();
     
             foreach ($sensores as $sensorId => $sensorNombre) {
                 $key = config('services.adafruit.key');
     
               
                 $response = Http::withHeaders([
                     'X-AIO-Key' => $key,
                 ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensorNombre}/data?start_time={$fechalimite}&end_time={$fechafinal}");
     
                 $data = $response->json();
     
                 
                 foreach ($data as $res) {
                     $createdAt = Carbon::parse($res['created_at']);
     
                     DB::table('infosensores')->updateOrInsert([
                         'sensor_id' => $sensorId,
                         'valor' => $res["value"],
                         'created_at' => $createdAt->toDateTimeString(),
                     ]);
                 }
             }
     
             Log::info("Datos nuevos procesados.");
         
             DB::table('cronjobs')->updateOrInsert([
                'nombre' => 'CargaDatosNuevos',
                'completada' => true
            ]);
  

    }

     public function CronJobParaEstadoBocina(){

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
                        Log::info("bocina encendida");
                        return response()->json(['message' => 'bocina encendida']);
                    }
                    else if($data == 0){
                        Log::info("bocina apagada");
                        return response()->json(['message' => 'bocina apagada']);
                    }
                 

                
               
            } else {
                return response()->json(['message' => 'Error al enviar los datos'], 500);
            }
     }
 
}

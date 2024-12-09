<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sensor;
use App\Models\InfoSensor;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CronJobController extends Controller
{
    public function CronJobParaGuardarDatos(){

        $sensores = Sensor::all()->toArray();

        $fechainicial = Carbon::now()->subDays(5)->startOfDay();
       $fechahoi = Carbon::now()->startOfDay();
       

      
       
 
        foreach($sensores as $sensor){

            $ultimafecha = $fechainicial->copy(); 

            while($ultimafecha->lessThan($fechahoi)){

                $fechalimite = $ultimafecha->copy()->setTimezone('UTC')->toIso8601String();
                $fechafinal = $ultimafecha->copy()->endOfDay()->setTimezone('UTC')->toIso8601String();
                

              

                $key = config('services.adafruit.key');
 
             
                $response = Http::withHeaders([
                    'X-AIO-Key' => $key,  
                ])->get("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.{$sensor['Nombre_Sensor']}/data?start_time={$fechalimite}&end_time={$fechafinal}");
            
                 
                $data = $response->json(); 
               

                
                
                foreach($data as $res){
                   
                    $createdAt = \Carbon\Carbon::parse($res['created_at']);
    
                  DB::table('infosensores')->updateOrInsert([
                    'sensor_id' => $sensor['id'],
                    'valor' => $res["value"],
                    'created_at' => $createdAt->toDateTimeString(),
                    
                  ]);

                  
        
                } 

                $ultimafecha->addDay();
              

            }
            
       
         }
 
         DB::table('cronjobs')->updateOrInsert([
            'nombre' => 'CargaInicial',
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
                        return response()->json(['message' => 'bocina encendida']);
                    }
                    else if($data == 0){
                        return response()->json(['message' => 'bocina apagada']);
                    }
                 

                
               
            } else {
                return response()->json(['message' => 'Error al enviar los datos'], 500);
            }
     }
 
}

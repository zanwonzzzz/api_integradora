<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;
use App\Models\Sensor;
use App\Models\MonitorSensor;
use App\Http\Controllers\AdafruitController;
use App\Models\User;
use Illuminate\Support\Facades\Http;
class MonitorController extends Controller
{

    //crear monitor
    public function crearm_s(request $request){
        $id = auth()->user()->id;

        $monitor = new Monitor();
        $monitor->user_id = $id;
        $monitor->Nombre_Monitor = $request->nombre;
        $monitor->save();

        $user = User::find($id);
        $user->monitor = 1;
        $user->save();
        

        return response()->json([
            'id' => $monitor->id,
            'nombre' => $monitor->Nombre_Monitor,
        ], 200);

    }

    //monitores que tiene un usuario
    public function monitor_usuario(){
        $id = auth()->user()->id;
        $monitores = Monitor::where('user_id', $id)->get();
        return response()->json([
            'msg' => 'Monitores',
            'data' => $monitores
        ], 200);
    }

    //borrar monitor
    public function borrarmonitor(int $id=0){
        if ($id != 0) {
          
            $monitor = Monitor::find($id);
            
            if ($monitor) {
                
                $monitor->delete();
                    return response()->json([
                        "msg" => "Monitor eliminado",
                        "data" => [
                            "monitor" => $monitor
                        ]
                    ], 200);
            } else {
                return response()->json(['msg' => 'Monitor no encontrado'], 404);
            }
        } else {
           
            $monitoresBorrados = Monitor::onlyTrashed()->get();
            return response()->json([
                'msg' => 'Monitores eliminadas',
                'data' => $monitoresBorrados
            ], 200);
        }
    }

    //elegir sensores
    public function elegir_sensores(int $idmonitor=0,int $idsensor=0){
        
    
         
         $monitor = auth()->user()->monitor()->find($idmonitor); 
        //$monitor = Monitor::find($idmonitor);
        $sensor_id = Sensor::find($idsensor);
       
        $monitor->sensores()->attach($sensor_id);

        //$ada = new AdafruitController();
        //$ada->SensorAda();
      

    }

    
    //borrar sensores q eligio
   /*   public function eliminar_sensores(int $idmonitor=0){
        
        $adafruitsensores = [];
        $key = config('services.adafruit.key');
        $monitor = auth()->user()->monitor()->find($idmonitor);
       

        $adafruitController = new AdafruitController();

        
        $sensoresEliminados = $adafruitController->BorrarSensores($idmonitor);
        $monitor->sensores()->detach($sensoresEliminados);
    
        
        $sensoresActualizados = $adafruitController->AdafruitSensor();

    
    if (empty($sensoresActualizados)) {
        $value = 'logout'; 
    } else {
        $value = implode(',', $sensoresActualizados); 
    }


    //$controler = new MonitorController();
    //$controler->borrarmonitor($idmonitor);

    
    $response = Http::withHeaders([
        'X-AIO-Key' => $key,
    ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
        'value' => $value,
    ]);


    
        
    } */

    public function eliminar_sensores(int $idmonitor=0,int $idsensor=0){
        
        $adafruitsensores = [];
        $key = config('services.adafruit.key');
        $monitor = auth()->user()->monitor()->find($idmonitor);
       

        $adafruitController = new AdafruitController();

        
        $sensoresEliminados = $adafruitController->BorrarSensores($idmonitor,$idsensor);

        if(!empty($sensoresEliminados))
        {
        $monitor->sensores()->detach($sensoresEliminados);
        }
    
        
        $sensoresActualizados = $adafruitController->AdafruitSensor();

    
     if (empty($sensoresActualizados)) {
        $value = 'logout'; 
     } else {
        $value = implode(',', $sensoresActualizados); 
     }


     //$controler = new MonitorController();
     //$controler->borrarmonitor($idmonitor);

    
     $response = Http::withHeaders([
        'X-AIO-Key' => $key,
     ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
        'value' => $value,
     ]);


    
        
    } 

    //obtener sensores de un monitor
    public function SensoresMonitor(int $idmonitor=0){
       
        
        $id = auth()->user()->id;
        $monitor = Monitor::find($idmonitor);
        $monitorsensor = MonitorSensor::where('monitor_id', $monitor->id)->pluck('sensor_id');
        $sensores = Sensor::whereIn('id', $monitorsensor)->get();
            
        
        
        
        

        return response()->json([
            'msg' => 'Sensores por monitor',
            'data' => [
                'monitor' => [
                    'id' => $monitor->id,
                    'sensores' => $sensores
                ],
                
            ]
        ], 200);
        
    }

    
}

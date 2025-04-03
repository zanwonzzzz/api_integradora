<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;
use App\Models\Sensor;
use App\Models\MonitorSensor;
use App\Http\Controllers\AdafruitController;
use App\Models\User;
use App\Models\MonitorMongo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MonitorController extends Controller
{

    //crear monitor
    public function crearm_s(request $request){

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'ubicacion' => 'nullable|string'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        }

        $id = auth()->user()->id;

        $monitor = new Monitor();
        $monitor->user_id = $id;
        $monitor->Nombre_Monitor = $request->nombre;
        $monitor->Ubicacion = $request->ubicacion;
        $monitor->save();



        $sendToMongoController = new SendToMongoDataController();
        $sendToMongoController->sendMonitorToMongo(new Request([
            'id' => $monitor->id,
            'user_id' => $monitor->user_id,
            'Nombre_Monitor' => $monitor->Nombre_Monitor
        ]));
        

        return response()->json([
            'id' => $monitor->id,
            'nombre' => $monitor->Nombre_Monitor,
        ], 200);

    }

    //monitores que tiene un usuario
    public function monitor_usuario(){
        
        $id = auth()->user()->id;
        $monitores = Monitor::where('user_id', $id)->get();
        return response()->json($monitores, 200);
    }


    //buscar por id monitor
    public function monitorPorId(int $idmonitor = 0){
        
        $monitor = Monitor::find($idmonitor);
        return response()->json($monitor, 200);
    }


     //actualizar monitor
     public function actualizarmonitor(int $idmonitor= 0,Request $request,int $idsensor=0)
     {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'ubicacion' => 'required|string'
        ]);
         if($validator->fails()){
            return response()->json($validator->errors(),422);
        } 

        $monitor = Monitor::find($idmonitor);
        if(!$monitor)
        {
            return response()->json('Monitor no encontrado',404);
        }

        $monitor->Nombre_Monitor = $request->nombre;
        $monitor->Ubicacion = $request->ubicacion;
        $monitor->save();

        /* $monitorMongo = new MonitorController();
        $monitorMongo->monitorUsuarioMongo(); */

        return response()->json("Monitor Actualizado",200);


     }

    //borrar monitor
    public function borrarmonitor(int $id=0){
        if ($id != 0) {
          
            $monitor = Monitor::find($id);
            
            if ($monitor) {

               
                
                $monitor->delete();
                $monitorMongo = new MonitorController();
                $monitorMongo->monitorUsuarioMongo();
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
    public function elegir_sensores($idmonitor,$idsensor){
        
    
         
        $monitor = auth()->user()->monitor()->find($idmonitor); 
        //dd($monitor);
        $sensor_id = Sensor::find($idsensor);
        //dd($sensor_id);
       
        $monitor->sensores()->attach($sensor_id);

        //$ada = new AdafruitController();
        //$ada->SensorAda();
        
        $monitorMongo = new MonitorController();
        $monitorMongo->monitorUsuarioMongo();

        $sendToMongoController = new SendToMongoDataController();
        $sendToMongoController->sendMonitorSensorToMongo(new Request([
            'monitor_id' => $monitor->id,
            'sensor_id' => $sensor_id->id
        ]));
    }

    public function elegirSensores(Request $request,$idmonitor){
        
    
         
        $monitor = auth()->user()->monitor()->find($idmonitor); 
        if(!$monitor)
        {
            return response()->json("Monitor no Encontrado",404);
        }
        //dd($monitor);
        $sensorIds = $request->input('sensores', []);

        $sensores = Sensor::find($sensorIds);
        //dd($sensores);

        if($sensores)
        {
            foreach($sensorIds as $sensor_id)
            {
                
                $monitor->sensores()->attach($sensor_id);
            }
            
    
            //$ada = new AdafruitController();
            //$ada->SensorAda();
            
            $monitorMongo = new MonitorController();
            $monitorMongo->monitorUsuarioMongo();
    
            $sendToMongoController = new SendToMongoDataController();
            $sendToMongoController->sendMonitorSensorToMongo(new Request([
                'monitor_id' => $monitor->id,
                'sensor_id' => $sensores->pluck('id')
            ])); 
        }
        else 
        {
            return response()->json("Sensores no encontrados",404);
        }
        
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
/* 
    
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

 */

        return response()->json('Sensor Eliminado');
    
        
    } 

 

    public function SensoresMonitor(int $idmonitor=0){
       
        
        $id = auth()->user()->id;
        $monitor = Monitor::find($idmonitor);
        $monitorsensor = MonitorSensor::where('monitor_id', $monitor->id)->pluck('sensor_id');
        $sensores = Sensor::whereIn('id', $monitorsensor)->get();
        //dd($sensores);
            
        
        
        return response()->json($sensores,200);
        
    }


    public function MonitorAMongo(int $id = 0)
    {

        $sensoresTodos = [];
        $user_id = auth()->user()->id;
        $monitor = Monitor::find($id);
        if(!$monitor){
            return response()->json("No se encuentra ese monitor",404);
        }
        $monitor->Activo++;
        $monitor->save();
        
        $sensores = new MonitorController();
        $sensoresMonitor = $sensores->SensoresMonitor($monitor->id)->getData();
        //dd($sensoresMonitor);
        foreach($sensoresMonitor as $data)
        {
           $sensoresTodos[] = $data->id;
        }
        //dd($sensoresTodos);
        $sendToMongoController = new SendToMongoDataController();
        $sendToMongoController->sendDatosMonitorToMongo(new Request([
            'user_id'=>$user_id,
            'id_monitor' => $monitor->id,
            'sensor' => $sensoresTodos,
            'Fecha' => Carbon::now()->toDateTimeString(),
        ]));
        


        return response()->json("Datos del monitor a mongo");

    }


    //mandar info del monitor a mongo
    
    public function monitorUsuarioMongo()
    {
        $id = auth()->user()->id;
        $monitores = Monitor::where('user_id', $id)->get();
        
        $monitoresSensores = [];
        
        $sendToMongoController = new SendToMongoDataController();
        
        foreach($monitores as $monitor) {
            $sensores = $this->sensoresDelMonitorUsuario($monitor->id); 
            
            
            $monitoresSensores[] = [
                'id_monitor' => $monitor->id,
                'sensor' => $sensores,
                'Fecha' => now()->toDateTimeString()
            ];
        }
        
        
        $result = $sendToMongoController->sendMonitorUsuario(new Request([
            'user_id' => $id, 
            'monitors' => $monitoresSensores
        ]));
        
        return response()->json("Datos actualizados en MongoDB");
    }

    public function sensoresDelMonitorUsuario(int $id = 0)
    {

        $sensoresTodos = [];
        $user_id = auth()->user()->id;
        $monitor = Monitor::find($id);
        if(!$monitor){
            return response()->json("No se encuentra ese monitor",404);
        }
        
        $sensores = new MonitorController();
        $sensoresMonitor = $sensores->SensoresMonitor($monitor->id)->getData();
        //dd($sensoresMonitor);
        foreach($sensoresMonitor as $data)
        {
           $sensoresTodos[] = $data->id;
        }
        //dd($sensoresTodos);
       /*  $sendToMongoController = new SendToMongoDataController();
        return $sendToMongoController->sendDatosMonitorToMongo(new Request([
            'id_monitor' => $monitor->id,
            'sensor' => $sensoresTodos,
            'Fecha' => Carbon::now()->toDateTimeString(),
        ])); */

        return $sensoresTodos;
        


    

    }


    
    
}

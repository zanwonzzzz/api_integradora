<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitor;
use App\Models\Sensor;

class MonitorController extends Controller
{

    //crear monitor
    public function crearm_s(request $request){
        $id = auth()->user()->id;

        $monitor = new Monitor();
        $monitor->user_id = $id;
        $monitor->Nombre_Monitor = $request->nombre;
        $monitor->save();

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
        $sensor_id = Sensor::find($id);
        
        $monitor->sensores()->attach($sensor_id);
      

    }

    //borrar sensores q eligio

    public function eliminar_sensores(int $idmonitor=0,int $idsensor=0){
        $monitor = auth()->user()->monitor()->find($idmonitor);
        $sensor_id = Sensor::find($id);
        
        $monitor->sensores()->detach($sensor_id);
    }
}

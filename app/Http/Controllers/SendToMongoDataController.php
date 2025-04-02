<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorMongo;
use App\Models\MonitorMongo;
use App\Models\MonitorSensorMongo;
use App\Models\UserMongo;
use App\Models\DatosMonitor;
use App\Models\Auditoria;
use App\Models\MonitorUser;

class SendToMongoDataController extends Controller
{
    public function sendUserToMongo(Request $request)
    {
        $data = new UserMongo();
        $data->id = $request->id;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->save();

        return null;
    }

    public function sendSensorToMongo(Request $request)
    {
        $data = new SensorMongo();
        $data->id = $request->id;
        $data->name = $request->Nombre_Sensor;
        $data->save();

        return null;
    }

    public function sendMonitorToMongo(Request $request)
    {
        $data = new MonitorMongo();
        $data->id = $request->id;
        $data->user_id = $request->user_id;
        $data->name = $request->Nombre_Monitor;
        $data->save();

        return null;
    }

    public function sendMonitorSensorToMongo(Request $request)
    {
        $data = new MonitorSensorMongo();
        $data->monitor_id = $request->monitor_id;
        $data->sensor_id = $request->sensor_id;
        $data->save();

        return null;
    }

    public function sendDatosMonitorToMongo(Request $request)
    {
        $data = new DatosMonitor();
        $data->id_monitor = $request->id_monitor;
        $data->sensor = array_values((array) $request->sensor);
        $data->Fecha = $request->Fecha;
        $data->save();

        return $data;
    }

    public function sendMonitorUsuario(Request $request)
    {
        $data = new MonitorUser();
        $data->user_id = $request->user_id;
        $data->monitors = $request->monitors;
        $data->save();

        return $data;
    }


    public function Auditorias(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = new Auditoria();
        $data->user_id = $user_id;
        $data->save();

        return "ola";
    }


    public function ConsultaAuditorias(Request $request)
    {
        $data = Auditoria::with('users')->get();

        return response()->json($data);
    }
}

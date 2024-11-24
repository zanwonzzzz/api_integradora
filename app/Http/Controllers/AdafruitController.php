<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sensor;

class AdafruitController extends Controller
{
    public function obtener(){

        $response = Http::get('https://io.adafruit.com/api/v2/zaniwonzzzzz/feeds/led/data');

        if($response->successful()){
            $data = $response->json();
            return response()->json($data);
        }
    }

    public function crear(request $request){
        $led = new Sensor();
        $led->value = $request->value;
        $led->save();

        $key = config('services.adafruit.key');

        $response = Http::WithHeaders([
            'X-AIO-Key' => $key]) ->post('https://io.adafruit.com/api/v2/zaniwonzzzzz/feeds/led/data', [
                'value' => $request->value
            ]);

            if ($response->successful()) {
                return response()->json(['message' => 'datos enviados correctamente']);
            } 
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\Gmail;
use Illuminate\Support\Facades\Validator;

class controlcontroller extends Controller
{
    public function index(int $id = 0)
    {
        $user = User::find($id);
        $user->cuenta_activa = true;
        $user->save();

        return response()->view('vistadecuentaactive',['user' => $user->name]);
    }

    public function reenvio(request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        }

        $email= $request->email;
        $user = User::where('email', $email)->firstOrFail();

        if($user->cuenta_activa == 0){
            $url= URL::temporarySignedRoute('activacion', now()->addMinutes(5), ['id' => $user->id]);
        Mail::to($user->email)->send(new Gmail($user,$url));

        /* $user->cuenta_activa = true;
        $user->save(); */

        return response()->json([
            "msg" => "reenvio exitoso"
        ]); 
        }
   }
}

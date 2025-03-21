<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\Gmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class controlcontroller extends Controller
{
    public function index(int $id = 0)
    {
        $user = User::find($id);
        $user->cuenta_activa = true;
        $user->save();

        return response()->view('vistadecuentaactive',['user' => $user->name]);
    }

    //verificar codigo
    public function CodigoVerificacion(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|integer',
            
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $user = User::findorfail($id);
        if($user)
        {
            if($user->codigo == $request->codigo)
            {
                $user->cuenta_activa = 1;
                $user->cuenta_activa_Admin = 1;
                $user->save();

                return response()->view('vistadecuentaactive',['user' => $user->name]);

               
            }
        }
        else{
            return response()->json("No encontrado");
        }
       
    }

    public function VistaVerificacion($id)
    {
       return response()->view('CodigoVerifica',['id'=>$id]);
    }

    public function reenvio(request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        }

        $email= $request->email;
        $user = User::where('email', $email)->firstOrFail();

        if($user->cuenta_activa == 0 && $user->cuenta_activa_Admin == 0)
        {
            $codigo = mt_rand(100000, 999999);
            $url= URL::temporarySignedRoute('activacion', now()->addMinutes(5), ['id' => $user->id]);
            Mail::to($user->email)->send(new Gmail($user,$url,$codigo));

            DB::table('users')->updateOrInsert(
                ['id' => $user->id],
                [
                    'codigo' => $codigo
                ]
            );

            return response()->json([
                "msg" => "reenvio exitoso"
            ]); 
        }
   }
}

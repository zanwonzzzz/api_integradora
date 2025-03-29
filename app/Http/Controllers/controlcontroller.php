<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\Gmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
        /* $validator = Validator::make($request->all(), [
            'codigo' => 'required|integer',
            
        ]); */
            $rules = [
                'codigo' => 'required|numeric|digits:6',
            ];
   
           $messages = [
               'codigo.required' => 'El campo codigo es obligatorio.',
               'codigo.numeric' => 'El campo codigo debe ser de tipo numerico.',
               'codigo.digits' => 'El campo codigo es de solo 6 digitos.'
           ];
           
       
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            //return response()->json($validator->errors(),400);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::findorfail($id);
        if($user)
        {
            if($user->codigo == $request->codigo)
            {
                $user->cuenta_activa = 1;
                $user->cuenta_activa_Admin = 1;
                $user->save();

               // return response()->view('vistadecuentaactive',['user' => $user->name]);
               return redirect()->back()->with('success', 'Código verificado correctamente. ✅')
                                         ->with('redirect_to', route('Inicio'));

               
            }
            else
            {
                return redirect()->back()->with('error', 'Código incorrecto. ❌');
            }
        }
        else{
            return redirect()->back()->with('error', 'Usuario No encontrado. ❌');
        }
       
    }

    public function VistaVerificacion($id)
    {
       return response()->view('CodigoVerifica',['id'=>$id]);
    }

    public function reenvio(request $request){

       /*  $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        } */
        if (!$request->hasValidSignature()) {
            return response()->json(["error" => 'No puedes solicitar reenvio si tu link no ha expirado'],403);
        }

        $rules = [
            'email' => 'required|email',
        ];

        $messages = [
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El campo email debe ser de tipo email.',
        ];
        
    
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
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
            ],200); 
        }
        else 
        {
            return response()->json([
                "msg" => "Tu cuenta ya esta activada no puedes solicitar reenvio"
            ],422); 
        }
   }


   //RECUPERAR CONTRASEÑA
   public function OlvidarContraseña(Request $request)
   {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'No se ha encontrado un usuario con ese correo.',
            ], 422);
        }
    
        $status = Password::sendResetLink(
            $request->only('email')
        );
       
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Correo de recuperación enviado correctamente.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se pudo enviar el correo. Verifica el correo proporcionado.',
            ], 422);
        }
   }

   public function ResetarContraseña(Request $request)
   {
       /*  $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]); */

        $rules = [
           'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ];

       $messages = [
           'token.required' => 'El campo token es obligatorio.',
           'password.required' => 'El campo contraseña es obligatorio.',
           'password.min' => 'El campo contraseña es de minimo 8 caracteres.'
       ];
       
   
    $validator = Validator::make($request->all(), $rules, $messages);
    if($validator->fails()){
        //return response()->json($validator->errors(),400);
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $reset = DB::table('password_resets')
    ->where('token', $request->token)
    ->first();

    if (!$reset) {
        return redirect()->back()->withErrors(['token' => 'Token inválido o expirado.'])->withInput();
    }

    $request->merge(['email' => $reset->email]);
    
        $status = Password::reset(
            $request->only('email','password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
            }
        );

        DB::table('password_resets')->where('token', $request->token)->delete();

        return redirect()->back()->with('success', 'Contraseña cambiada correctamente. ✅')
                                         ->with('redirect_to', route('Inicio'));

   }

}

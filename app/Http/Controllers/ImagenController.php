<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImagenController extends Controller
{
    public function SubirFoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $archivo = $request->file('archivo');

            $rutaCarpeta = '23170087/'; 
                $path = Storage::disk('s3')->put($rutaCarpeta, $archivo);

                $id = auth()->user()->id; 

                $mime = $archivo->getClientMimeType($path);

        DB::table('users')->updateOrInsert(
            ['id' => $id],
            [
                'fotoperfil' => $path,
                'mime_type' => $mime,
            ]
        );
    

               return response()->json(['path' => $path], 201);
    }

    public function MostrarFoto()
    {
       $id = auth()->user()->id; 
        $user =User::findorfail($id);

        $contenido = Storage::disk('s3')->get($user->fotoperfil);
       

        return response($contenido, 200)
            ->header('Content-Type', $user->mime_type); 
    }

    //SUBIR FOTOS DEL ESTADO DEL BEBE
   public function Fotobebe(Request $request){
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }

        $archivo = $request->file('archivo');

            $rutaCarpeta = 'estados'; 
                $path = Storage::disk('s3')->put($rutaCarpeta, $archivo);

           return response()->json(['path' => $path], 201);     
    } 

    public function RecuperarEstadoBebe(int $idestado = 0){
        if($idestado === 1 ){
            /* $contenido = Storage::disk('s3')->get('estados/bebetriste.webp');
            
            return response($contenido, 200)
            ->header('Content-Type', $user->mime_type);  */
            return response()->json('Imagen del niño feliz');
        }
        else if ($idestado === 2){
            return response()->json('Imagen del niño serio');
        }
        else if ($idestado === 3){
            return response()->json('Imagen del niño triste');
        }
    }
}

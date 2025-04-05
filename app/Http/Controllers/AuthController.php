<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Gmail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use App\Http\Controllers\AdafruitController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SendToMongoDataController;
use App\Http\Controllers\MonitorController;

class AuthController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
    
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();


        if($user->mime_type == "1")
        {
            $monitorMongo = new MonitorController();
            $monitorMongo->monitorUsuarioMongo();
        }

       
       
    
        DB::table('tabla_tokens')->updateOrInsert([
            'user_id' => $user->id,
            'token' => $token
        ]);
    
        
        

        if ($user->cuenta_activa == 0 || $user->cuenta_activa_Admin == 0) {
            return response()->json(['error' => 'Cuenta no activada.'], 403);
        } else {
           
            return response()->json([
                'token' => $token
            ]);
        }

       
       /* return response()->json([
            'laravel_token' => $token,
            //'adonis_token' => $atoken,
        ]);*/
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        $credentials = $request->only('email','name','password');
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
            'name' => 'required',
            'password' => 'required|string|min:6',
            'foto' => 'nullable|string',
            'rol_id'=>'nullable|number'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first() 
            ], 422);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));


        $codigo = mt_rand(100000, 999999);
        $url= URL::temporarySignedRoute('activacion', now()->addMinutes(5), ['id' => $user->id]);
        Mail::to($user->email)->send(new Gmail($user,$url,$codigo));

        DB::table('users')->updateOrInsert(
            ['id' => $user->id],
            [
                'codigo' => $codigo,
                'codigo_created_at' => now(),
                'mime_type'=>"0"
            ]
        );

        $sendToMongoController = new SendToMongoDataController();
        $sendToMongoController->sendUserToMongo(new Request([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]));

        return response()->json([
            
                'email' => $user->email,
                'name' => $user->name,
                'password' => $user->password

            
        ], 201);
    }


    public function ActualizarUsuario(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|min:3|max:50', 
            'password' => 'sometimes|string|min:6'     
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $user = auth()->user();
        $updated = false;
        
        if ($request->has('name') && $request->name !== $user->name) {
            $user->name = $request->name;
            $updated = true;
        }
        
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
            $updated = true;
        }
        
        if ($updated) {
            $user->save();
            return response()->json([
                'msg' => 'Usuario actualizado correctamente'
            ], 200);
        }
        
        return response()->json([
            'msg' => 'No hay cambios para actualizar'
        ], 200);
    }

   /*  public function SalidaUsuario(){

        $key = config('services.adafruit.key');


        $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
            'value' => 'logout'
        ]);
    } */

    public function totalusuarios(){

        $usuarios=  User::where('cuenta_activa')->count();

        return response()->json([
            'total_usuarios' => $usuarios
        ], 200);
    }

    public function toatalmonitores(){

        $monitores=  Monitor::all()->count();
        return response()->json([
            'total_monitores' => $monitores
        ], 200);
    }
}

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
use App\Http\Controllers\AdafruitController;
use Illuminate\Support\Facades\Http;
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
        $credentials = request(['name', 'email', 'password']);
    
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        
        

        if ($user->cuenta_activa == 0 ) {
            return response()->json(['error' => 'Cuenta no activada.'], 403);
        } else {

            if($user->monitor == 1){
               
                $ada = new AdafruitController();
                $ada->AdafruitSensor();
            }
           
           
            return response()->json([
                'laravel_token' => $token,
               // 'oswi_token' => $atoken,
            ]);
        }
    
       /* $response = Http::withHeaders([])->post('http://192.168.253.29:9090/api/auth/login', [
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);
    
        $data = $response->json();
        $atoken = $data['access_token'] ?? null;
    
        if (!$atoken) {
            return response()->json(['error' => 'Token no recibido'], 500);
        }*/
    
       // $id = auth()->user()->id; 
    
        DB::table('tabla_tokens')->updateOrInsert([
            'user_id' => $user->id,
            'token' => $token
        ]);
    
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
            'foto' => 'nullable|string'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        }

        $user = User::create(array_merge(
            $validator->validate(),
            ['password' => bcrypt($request->password)]
        ));

         /* $response = Http::post('http://192.168.253.29:9090/api/auth/register', [
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => $credentials['password'],
          ]);*/

          $url= URL::temporarySignedRoute('activacion', now()->addMinutes(5), ['id' => $user->id]);
        Mail::to($user->email)->send(new Gmail($user,$url));

        return response()->json([
            
                'email' => $user->email,
                'name' => $user->name,
                'password' => $user->password

            
        ], 201);
    }


    public function ActualizarUsuario(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|string|min:6'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),422);
        }

        $id = auth()->user()->id;
        $user = User::find($id);
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->save();
       
    }

    public function SalidaUsuario(){

        $key = config('services.adafruit.key');


        $response = Http::withHeaders([
            'X-AIO-Key' => $key,  
        ])->post("https://io.adafruit.com/api/v2/TomasilloV/feeds/sensores.bocina/data", [
            'value' => 'logout'
        ]);
    }
}

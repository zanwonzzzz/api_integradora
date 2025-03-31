<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\NotificacionReseteoContraseña;
use Illuminate\Support\Facades\URL;
use Jenssegers\Mongodb\Eloquent\HybridRelations;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes,HybridRelations;

    protected $connection = 'mysql';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'cuenta_activa',
        'cuenta_activa_Admin',
        'codigo',
        'codigo_created_at',
        'fotoperfil',
        'mime_type',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'codigo_created_at' => 'datetime'
    ];
    public function getJWTIdentifier() //este metodo indica que atributo del modelo debe usarse como el identificador del usuario dentro del token  jwt
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()    //este metodo permite que otras cosas deseamos agregar en el token JWT
    {
        return ['rol' => $this->rol_id ];
    }

    public function token(){
        return $this->hasMany(Token::class);
    }

    public function monitor (){
        return $this->hasMany(Monitor::class);
    }

    public function sendPasswordResetNotification($token)
    {
       /*  $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));
 */
        $url= URL::temporarySignedRoute('password.reset', now()->addMinutes(10), ['token' => $token,
            'email' => $this->email,]);

        $this->notify((new NotificacionReseteoContraseña($url)));
    }

    public function auditorias()
    {
        return $this->hasMany(Auditoria::class);
    }
}

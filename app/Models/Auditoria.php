<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'Auditorias';

    public $timestamps = false; 

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}

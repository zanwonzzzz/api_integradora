<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class DatosMonitor extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'DatosMonitores';


    public $timestamps = false; 
    
    protected $fillable = ['id_monitor', 'sensor', 'Fecha'];
}

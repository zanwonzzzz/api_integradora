<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class SensorData extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'InformacionSensor';
    public $timestamps = false;

    protected $guarded = [];
}
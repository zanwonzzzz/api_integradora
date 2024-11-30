<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MonitorSensor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monitores_sensores';


    public function infosensor(){
        return $this->hasMany(InfoSensor::class);
    }
}

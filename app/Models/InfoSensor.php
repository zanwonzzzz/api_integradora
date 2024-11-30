<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InfoSensor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "infosensores";

    public function monitor_sensor(){
        return $this->belongsTo(MonitorSensor::class);
    }
}

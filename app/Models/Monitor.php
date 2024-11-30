<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Monitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'monitores';

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function sensores(){
        return $this->belongsToMany(Sensor::class, 'monitores_sensores', 'monitor_id', 'sensor_id');
    }
}

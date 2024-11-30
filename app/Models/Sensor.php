<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;
    protected $table = 'sensores';

    public function user(){
        return $this->belongsTo(User::class);
    }

   public function monitores (){
      return $this->belongsToMany(Monitor::class, 'monitores_sensores','monitor_id', 'sensor_id');
   }
}

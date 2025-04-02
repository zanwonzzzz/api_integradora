<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class MonitorUser extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'SelectMonitores';

    public $timestamps = false; 
    
    protected $fillable = ['user_id', 'monitors'];
}

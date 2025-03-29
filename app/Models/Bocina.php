<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Bocina extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'BocinaEstado';
    public $timestamps = false;

    protected $guarded = [];
    use HasFactory;
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Token extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tabla_tokens';

    protected $fillable = [
        'user_id',
        'token',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}

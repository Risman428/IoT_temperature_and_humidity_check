<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dht22 extends Model
{
    protected $guarded = ['id'];
    protected $fillable = [
        'temperature',
        'humidity',
        'target_temperature',
        'led',
        'buzzer',
        'servo'
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwitchLamp extends Model
{
    protected $table = 'switch_lamp';

    protected $fillable = [
        'lampu1',
        'lampu2',
        'lampu3',
        'lampu4',
        'lampu5',
        'lampu6',
    ];

    public $timestamps = false; // karena tabel ini tidak punya created_at / updated_at
}

<?php

use App\Http\Controllers\Dht22Controller;
use Illuminate\Support\Facades\Route;

use App\Models\Dht22;

Route::get('/', function () {
    $dht = Dht22::first();   // ambil data dari database
    return view('welcome', compact('dht'));
});

// route pengambilan data temperature and humidity
Route::get('/update-data/{tmp}/{hmd}', [Dht22Controller::class, 'updateData']);
Route::get('/get-data', [Dht22Controller::class, 'getData']); 
//route control input manual di laravel
Route::post('/control', [Dht22Controller::class, 'setControl']);
Route::get('/control', [Dht22Controller::class, 'getControl']);
//route pengambilan data status LED dan Buzzer
Route::get('/update-device/{led}/{buzzer}', [Dht22Controller::class, 'updateDevice']);
//route control servo di laravel
Route::get('/update-servo/{status}', [Dht22Controller::class, 'updateServo']);
Route::get('/servo-control', [Dht22Controller::class, 'servoControl']);

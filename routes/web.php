<?php

use App\Http\Controllers\Dht22Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwitchLampController;
use App\Models\Dht22;
use App\Models\SwitchLamp;

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

// route get semua lampu
Route::get('/lamp', function () {
    $data = SwitchLamp::first();

    return response()->json([
        'lampu1' => $data->lampu1,
        'lampu2' => $data->lampu2,
        'lampu3' => $data->lampu3,
        'lampu4' => $data->lampu4,
        'lampu5' => $data->lampu5,
        'lampu6' => $data->lampu6,
    ]);
});
// route update lampu -> ex: /lamp/lampu1?status=on
Route::get('/lamp/{lamp}', [SwitchLampController::class, 'updateSwitch']);

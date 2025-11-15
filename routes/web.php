<?php

use App\Http\Controllers\Dht22Controller;
use Illuminate\Support\Facades\Route;

use App\Models\Dht22;

Route::get('/', function () {
    $dht = Dht22::first();   // ambil data dari database
    return view('welcome', compact('dht'));
});


Route::get('/update-data/{tmp}/{hmd}', [Dht22Controller::class, 'updateData']);
Route::get('/get-data', [Dht22Controller::class, 'getData']); 

Route::post('/control', [Dht22Controller::class, 'setControl']);
Route::get('/control', [Dht22Controller::class, 'getControl']);

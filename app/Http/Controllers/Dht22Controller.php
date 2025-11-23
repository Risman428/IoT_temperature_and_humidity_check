<?php

namespace App\Http\Controllers;

use App\Models\Dht22;
use Illuminate\Http\Request;

class Dht22Controller extends Controller
{
    public function __construct()
    {
        $dht = Dht22::count();
        if ($dht == 0) {
            Dht22::create([
                'temperature' => 0,
                'humidity' => 0,
                'target_temperature' => 30, // default target
                'lamp' => 'OFF'
            ]);
        }
    }

    //ini untuk menyimpan data suhu yang di input manual
    public function index()
    {
        $dht = Dht22::first(); // ambil data pertama di tabel

        return view('control', [
            'dht' => $dht
        ]);
    }

    // Update data dari NodeMCU
    public function updateData($tmp, $hmd)
    {
        $dht = Dht22::first();
        $dht->temperature = $tmp;
        $dht->humidity = $hmd;
        $dht->save();   

        return response()->json(['message' => 'Data updated successfully']);
    }

    //UpdateData led & buzzer
    public function updateDevice($led, $buzzer)
    {
        $dht = Dht22::first();

        $dht->led = $led;
        $dht->buzzer = $buzzer;
        $dht->save();

        return response()->json(['message' => 'Device status updated']);
    }


    // Ambil data sensor
    public function getData()
    {
        $dht = Dht22::first();

        return response()->json([
            'temperature' => $dht->temperature,
            'humidity' => $dht->humidity,
            'target_temperature' => $dht->target_temperature,
            'led' => $dht->led,
            'buzzer' => $dht->buzzer,
        ]);
    }

    // Form POST dari web (tanpa JSON)
    public function setControl(Request $request)
    {
        $request->validate([
            'target_temperature' => 'required|numeric'
        ]);

        $dht = Dht22::first();

        if (!$dht) {
            $dht = Dht22::create([
                'temperature' => 0,
                'humidity' => 0,
                'target_temperature' => $request->target_temperature,
                'lamp' => 'OFF'
            ]);
        } else {
            $dht->update([
                'target_temperature' => $request->target_temperature
            ]);
        }

        return redirect()->back()->with('success', 'Target suhu berhasil diperbarui');
    }

    // API GET untuk Arduino
    public function getControl()
    {
        $dht = Dht22::first();

        return response()->json([
            'target_temperature' => $dht ? $dht->target_temperature : 30,
            'lamp' => $dht ? $dht->lamp : 'OFF'
        ]);
    }
}

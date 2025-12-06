<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SwitchLamp;

class SwitchLampController extends Controller
{
    // Ambil status semua lampu
    public function index()
    {
        // $switch = SwitchLamp::first();

        // if (!$switch) {
        //     $switch = SwitchLamp::create([
        //         'lampu1' => 'off',
        //         'lampu2' => 'off',
        //         'lampu3' => 'off',
        //         'lampu4' => 'off',
        //         'lampu5' => 'off',
        //         'lampu6' => 'off',
        //     ]);
        // }

        // return response()->json($switch);
    }

    public function updateSwitch(Request $request, $lamp)
    {
        $switch = SwitchLamp::first();

        if (!$switch) {
            return response()->json(['message' => 'Database belum dibuat'], 500);
        }

        if (!in_array($lamp, ['lampu1','lampu2','lampu3','lampu4','lampu5','lampu6'])) {
            return response()->json(['message' => 'Invalid lamp'], 400);
        }

        // pastikan hanya on/off
        $status = $request->status == 'on' ? 'on' : 'off';

        $switch->{$lamp} = $status;
        $switch->save();

        return response()->json([
            'lamp' => $lamp,
            'status' => $status
        ]);
    }

}

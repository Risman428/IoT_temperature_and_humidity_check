<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SwitchLamp;

class SwitchLampSeeder extends Seeder
{
    public function run()
    {
        SwitchLamp::create([
            'lampu1' => 'off',
            'lampu2' => 'off',
            'lampu3' => 'off',
            'lampu4' => 'off',
            'lampu5' => 'off',
            'lampu6' => 'off',
        ]);
    }
}

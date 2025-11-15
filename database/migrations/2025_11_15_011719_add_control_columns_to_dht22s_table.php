<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dht22s', function (Blueprint $table) {
            $table->float('target_temperature')->default(0);
            $table->string('lamp')->default('AUTO'); // AUTO, ON, OFF
        });
    }

    public function down(): void
    {
        Schema::table('dht22s', function (Blueprint $table) {
            $table->dropColumn(['target_temperature', 'lamp']);
        });
    }

};

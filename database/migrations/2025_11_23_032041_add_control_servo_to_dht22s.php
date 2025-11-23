<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('dht22s', function (Blueprint $table) {
            $table->boolean('servo')->default(0);
        });
    }


    public function down()
    {
        Schema::table('dht22s', function (Blueprint $table) {
            $table->dropColumn('servo');
        });
    }

};

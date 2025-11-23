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
            $table->boolean('led')->default(0);      // 0 = OFF, 1 = ON
            $table->boolean('buzzer')->default(0);   // 0 = OFF, 1 = ON
        });
    }

    public function down(): void
    {
        Schema::table('dht22s', function (Blueprint $table) {
            $table->dropColumn(['led', 'buzzer']);
        });
    }

};
